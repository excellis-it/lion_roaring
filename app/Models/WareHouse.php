<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WareHouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location_lat',
        'location_lng',
        'address',
        'country_id',
        'service_range',
        'is_active',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function warehouseProducts()
    {
        return $this->hasMany(WarehouseProduct::class, 'warehouse_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'warehouse_products', 'warehouse_id', 'product_id');
    }

    // Admins who can manage this warehouse
    public function admins()
    {
        return $this->belongsToMany(User::class, 'user_warehouses', 'warehouse_id', 'user_id')
            ->withTimestamps();
    }
}
