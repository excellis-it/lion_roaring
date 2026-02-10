<?php

namespace App\Services;

use App\Models\MarketMaterial;
use App\Models\MarketMaterialRate;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\WarehouseProduct;
use App\Models\EstoreSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MarketRateService
{
    private const GRAMS_PER_TROY_OUNCE = 31.1034768;

    public static function refreshIfStale(): void
    {
        $lastFetched = MarketMaterialRate::max('fetched_at');
        $refreshMinutes = self::getRefreshIntervalMinutes();
        if ($lastFetched && Carbon::parse($lastFetched)->diffInMinutes(now()) < $refreshMinutes) {
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

        $primary = strtolower((string) self::getPrimaryProvider());
        $secondary = $primary === 'goldapi' ? 'metalpriceapi' : 'goldapi';

        $primaryRates = self::fetchRatesFromProvider($primary, $materials);
        $ratesByMaterialId = $primaryRates;

        $missingMaterialIds = $materials
            ->pluck('id')
            ->diff(array_keys($ratesByMaterialId))
            ->values()
            ->all();

        if (!empty($missingMaterialIds)) {
            $missingMaterials = $materials->whereIn('id', $missingMaterialIds);
            $fallbackRates = self::fetchRatesFromProvider($secondary, $missingMaterials);
            $ratesByMaterialId = $ratesByMaterialId + $fallbackRates;
        }

        if (empty($ratesByMaterialId)) {
            return;
        }

        foreach ($ratesByMaterialId as $materialId => $payload) {
            $ratePerGram = $payload['rate_per_gram'];
            $usdPerOunce = $payload['usd_per_ounce'];
            $apiTimestamp = $payload['api_timestamp'];
            $fetchedAt = $payload['fetched_at'];

            MarketMaterialRate::create([
                'market_material_id' => $materialId,
                'base_currency' => 'USD',
                'usd_per_ounce' => $usdPerOunce,
                'rate_per_gram' => $ratePerGram,
                'api_timestamp' => $apiTimestamp,
                'fetched_at' => $fetchedAt,
            ]);

            self::updateMarketPricedProducts($materialId, $ratePerGram, $fetchedAt);
        }
    }

    private static function fetchRatesFromProvider(string $provider, $materials): array
    {
        return $provider === 'goldapi'
            ? self::fetchFromGoldApi($materials)
            : self::fetchFromMetalPriceApi($materials);
    }

    private static function fetchFromMetalPriceApi($materials): array
    {
        $apiKey = config('services.metalpriceapi.key');
        if (!$apiKey) {
            Log::warning('MetalPriceAPI key is missing.');
            return [];
        }

        $codes = $materials->pluck('code')->unique()->values()->all();
        if (empty($codes)) {
            return [];
        }

        $url = 'https://api.metalpriceapi.com/v1/latest';

        try {
            $response = Http::timeout(10)->get($url, [
                'api_key' => $apiKey,
                'base' => 'USD',
                'currencies' => implode(',', $codes),
            ]);

            if (!$response->successful()) {
                Log::warning('MetalPriceAPI request failed', ['status' => $response->status()]);
                return [];
            }

            $data = $response->json();
            if (!($data['success'] ?? false)) {
                Log::warning('MetalPriceAPI response unsuccessful', ['response' => $data]);
                return [];
            }

            $rates = $data['rates'] ?? [];
            $apiTimestamp = $data['timestamp'] ?? null;
            $fetchedAt = now();

            $result = [];
            foreach ($materials as $material) {
                $code = strtoupper($material->code);
                $usdPerOunceKey = 'USD' . $code;
                $usdPerOunce = $rates[$usdPerOunceKey] ?? null;
                if ($usdPerOunce === null) {
                    continue;
                }

                $result[$material->id] = [
                    'usd_per_ounce' => (float) $usdPerOunce,
                    'rate_per_gram' => (float) $usdPerOunce / self::GRAMS_PER_TROY_OUNCE,
                    'api_timestamp' => $apiTimestamp,
                    'fetched_at' => $fetchedAt,
                ];
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error('MetalPriceAPI error: ' . $e->getMessage());
            return [];
        }
    }

    private static function fetchFromGoldApi($materials): array
    {
        $baseUrl = rtrim((string) config('services.goldapi.base', 'https://api.gold-api.com'), '/');
        if (!$baseUrl) {
            Log::warning('Gold API base URL is missing.');
            return [];
        }

        $result = [];
        foreach ($materials as $material) {
            $code = strtoupper($material->code);
            $url = $baseUrl . '/price/' . $code;

            try {
                $response = Http::timeout(10)->get($url);
                if (!$response->successful()) {
                    Log::warning('Gold API request failed', ['status' => $response->status(), 'code' => $code]);
                    continue;
                }

                $data = $response->json();
                if (!isset($data['price'])) {
                    Log::warning('Gold API response missing price', ['response' => $data]);
                    continue;
                }

                $usdPerOunce = (float) $data['price'];
                $updatedAt = $data['updatedAt'] ?? null;
                $apiTimestamp = $updatedAt ? strtotime($updatedAt) : null;
                // Use the current time as the fetched_at so we don't repeatedly re-fetch
                // when the API's reported updatedAt is older than our last fetch time.
                $fetchedAt = now();

                $result[$material->id] = [
                    'usd_per_ounce' => $usdPerOunce,
                    'rate_per_gram' => $usdPerOunce / self::GRAMS_PER_TROY_OUNCE,
                    'api_timestamp' => $apiTimestamp,
                    'fetched_at' => $fetchedAt,
                ];
            } catch (\Throwable $e) {
                Log::error('Gold API error: ' . $e->getMessage(), ['code' => $code]);
            }
        }

        return $result;
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

            // Determine quantity in grams depending on unit (default is grams 'g')
            $unit = strtolower($product->market_unit ?? 'g');
            $gramsQty = (float) $product->market_grams;
            if ($unit === 'oz') {
                $gramsQty = $gramsQty * self::GRAMS_PER_TROY_OUNCE; // convert ounces to grams
            }

            $newPrice = (float) $ratePerGram * $gramsQty;

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

    private static function getPrimaryProvider(): string
    {
        $setting = EstoreSetting::first();
        if ($setting && !empty($setting->market_rate_primary)) {
            return $setting->market_rate_primary;
        }

        return (string) config('services.market_rates.primary', 'metalpriceapi');
    }

    private static function getRefreshIntervalMinutes(): int
    {
        $setting = EstoreSetting::first();
        $value = (int) ($setting->market_rate_refresh_value ?? 12);
        $unit = strtolower((string) ($setting->market_rate_refresh_unit ?? 'hour'));

        if ($value < 1) {
            $value = 1;
        }

        switch ($unit) {
            case 'minute':
                return $value;
            case 'day':
                return $value * 60 * 24;
            case 'month':
                return $value * 60 * 24 * 30;
            case 'hour':
            default:
                return $value * 60;
        }
    }
}
