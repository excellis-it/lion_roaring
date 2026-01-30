<?php

namespace App\Services;

use App\Models\MarketMaterial;
use App\Models\MarketMaterialRate;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\WarehouseProduct;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MarketRateService
{
    private const GRAMS_PER_TROY_OUNCE = 31.1034768;

    public static function refreshIfStale(): void
    {
        $lastFetched = MarketMaterialRate::max('fetched_at');
        if ($lastFetched && Carbon::parse($lastFetched)->diffInHours(now()) < 12) {
            return;
        }

        self::fetchAndStoreRates();
    }

    public static function getLatestRateForMaterial(int $materialId): ?MarketMaterialRate
    {
        self::refreshIfStale();

        return MarketMaterialRate::where('market_material_id', $materialId)
            ->orderByDesc('fetched_at')
            ->first();
    }

    private static function fetchAndStoreRates(): void
    {
        $materials = MarketMaterial::where('is_active', true)->orderBy('sort_order')->get();
        if ($materials->isEmpty()) {
            return;
        }

        $codes = $materials->pluck('code')->unique()->values()->all();
        $currencies = implode(',', $codes);

        $apiKey = config('services.metalpriceapi.key');
        if (!$apiKey) {
            Log::warning('MetalPriceAPI key is missing.');
            return;
        }

        $url = 'https://api.metalpriceapi.com/v1/latest';

        try {
            $response = Http::timeout(10)->get($url, [
                'api_key' => $apiKey,
                'base' => 'USD',
                'currencies' => $currencies,
            ]);

            if (!$response->successful()) {
                Log::warning('MetalPriceAPI request failed', ['status' => $response->status()]);
                return;
            }

            $data = $response->json();
            if (!($data['success'] ?? false)) {
                Log::warning('MetalPriceAPI response unsuccessful', ['response' => $data]);
                return;
            }

            $rates = $data['rates'] ?? [];
            $apiTimestamp = $data['timestamp'] ?? null;
            $fetchedAt = now();

            foreach ($materials as $material) {
                $code = strtoupper($material->code);
                $usdPerOunceKey = 'USD' . $code;
                $usdPerOunce = $rates[$usdPerOunceKey] ?? null;

                if ($usdPerOunce === null) {
                    continue;
                }

                $ratePerGram = (float) $usdPerOunce / self::GRAMS_PER_TROY_OUNCE;

                MarketMaterialRate::create([
                    'market_material_id' => $material->id,
                    'base_currency' => 'USD',
                    'usd_per_ounce' => $usdPerOunce,
                    'rate_per_gram' => $ratePerGram,
                    'api_timestamp' => $apiTimestamp,
                    'fetched_at' => $fetchedAt,
                ]);

                self::updateMarketPricedProducts($material->id, $ratePerGram, $fetchedAt);
            }
        } catch (\Throwable $e) {
            Log::error('MetalPriceAPI error: ' . $e->getMessage());
        }
    }

    private static function updateMarketPricedProducts(int $materialId, float $ratePerGram, $fetchedAt): void
    {
        $products = Product::where('is_market_priced', true)
            ->where('market_material_id', $materialId)
            ->get();

        foreach ($products as $product) {
            if ($product->product_type !== 'simple' || !$product->market_grams) {
                continue;
            }

            $newPrice = (float) $ratePerGram * (float) $product->market_grams;

            $product->price = $newPrice;
            $product->sale_price = null;
            $product->market_rate_per_gram = $ratePerGram;
            $product->market_rate_at = $fetchedAt;
            $product->is_free = false;
            $product->save();

            ProductVariation::where('product_id', $product->id)->update([
                'price' => $newPrice,
                'sale_price' => null,
                'before_sale_price' => null,
            ]);

            WarehouseProduct::where('product_id', $product->id)->update([
                'price' => $newPrice,
                'before_sale_price' => null,
            ]);
        }
    }
}
