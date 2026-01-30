<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketMaterialRate extends Model
{
    protected $fillable = [
        'market_material_id',
        'base_currency',
        'usd_per_ounce',
        'rate_per_gram',
        'api_timestamp',
        'fetched_at',
    ];

    protected $casts = [
        'fetched_at' => 'datetime',
        'usd_per_ounce' => 'decimal:8',
        'rate_per_gram' => 'decimal:8',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(MarketMaterial::class, 'market_material_id');
    }
}
