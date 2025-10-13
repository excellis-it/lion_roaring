<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderEmailTemplate extends Model
{
    use HasFactory;

    protected $table = 'order_email_templates';

    protected $fillable = [
        'title',
        'slug',
        'order_status_id',
        'subject',
        'body',
        'is_active',
    ];

    /**
     * Optional: relation to OrderStatus
     */
    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }
}
