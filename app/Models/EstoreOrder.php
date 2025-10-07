<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstoreOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'order_number',
        'is_pickup',
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'pincode',
        'country',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'total_amount',
        'status',
        'payment_status',
        'notes',
        'credit_card_fee',
        'payment_type',
        'warehouse_name',
        'warehouse_address',
        'promo_code',
        'promo_discount',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(EstoreOrderItem::class, 'order_id');
    }

    public function payments()
    {
        return $this->hasMany(EstorePayment::class, 'order_id');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFullAddressAttribute()
    {
        $address = $this->address_line_1;
        if ($this->address_line_2) {
            $address .= ', ' . $this->address_line_2;
        }
        $address .= ', ' . $this->city . ', ' . $this->state . ' ' . $this->pincode . ', ' . $this->country;
        return $address;
    }

    public static function getStatusOptions()
    {
        return [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled'
        ];
    }

    public static function getPaymentStatusOptions()
    {
        return [
            'pending' => 'Pending',
            'paid' => 'Paid',
            'failed' => 'Failed',
            'refunded' => 'Refunded'
        ];
    }

    public function getStatusBadgeClassAttribute()
    {
        $classes = [
            'pending' => 'bg-warning',
            'processing' => 'bg-info',
            'shipped' => 'bg-primary',
            'delivered' => 'bg-success',
            'cancelled' => 'bg-danger'
        ];
        return $classes[$this->status] ?? 'bg-secondary';
    }

    public function getPaymentStatusBadgeClassAttribute()
    {
        $classes = [
            'pending' => 'bg-warning',
            'paid' => 'bg-success',
            'failed' => 'bg-danger',
            'refunded' => 'bg-secondary'
        ];
        return $classes[$this->payment_status] ?? 'bg-secondary';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!$order->order_number) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
        });
    }

    // warehouse
    public function warehouse()
    {
        return $this->belongsTo(WareHouse::class, 'warehouse_id');
    }
}
