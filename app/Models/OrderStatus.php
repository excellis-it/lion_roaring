<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;

    protected $table = 'order_statuses';

    protected $fillable = [
        'name',
        'slug',
        'color',
        'sort_order',
        'is_active',
    ];

    /**
     * Optional: relation to orders
     */
    public function orders()
    {
        return $this->hasMany(EstoreOrder::class, 'status');
    }

    /**
     * Optional: relation to OrderEmailTemplate
     */
    public function emailTemplate()
    {
        return $this->hasMany(OrderEmailTemplate::class, 'order_status_id');
    }
}
