<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstoreSetting extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'shipping_cost',
        'delivery_cost',
        'tax_percentage',
        'is_pickup_available',
        'credit_card_percentage',
        'refund_max_days',
        'shipping_rules',
        'max_order_quantity',
    ];

    protected $casts = [
        'shipping_rules' => 'array',
        'is_pickup_available' => 'boolean',
    ];

    /**
     * Return shipping and delivery cost for a given total quantity based on rules.
     * Rules format: array of { min_qty, max_qty|null, shipping_cost, delivery_cost }
     */
    public function getShippingForQuantity(int $qty): array
    {
        $rules = $this->shipping_rules ?? [];

        // Ensure rules are sorted by min_qty asc
        usort($rules, function ($a, $b) {
            return ($a['min_qty'] ?? 0) <=> ($b['min_qty'] ?? 0);
        });

        foreach ($rules as $rule) {
            $min = isset($rule['min_qty']) ? (int)$rule['min_qty'] : 0;
            $max = isset($rule['max_qty']) && $rule['max_qty'] !== null ? (int)$rule['max_qty'] : null;

            if ($qty >= $min && (is_null($max) || $qty <= $max)) {
                return [
                    'shipping_cost' => isset($rule['shipping_cost']) ? (float)$rule['shipping_cost'] : 0.0,
                    'delivery_cost' => isset($rule['delivery_cost']) ? (float)$rule['delivery_cost'] : 0.0,
                ];
            }
        }

        // fallback to legacy flat rates
        return [
            'shipping_cost' => (float)($this->shipping_cost ?? 0),
            'delivery_cost' => (float)($this->delivery_cost ?? 0),
        ];
    }
}
