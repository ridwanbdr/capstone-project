<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'order_date',
        'description',
        'status',
    ];

    protected $casts = [
        'order_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->order_id)) {
                $latestId = self::max('id');
                $nextId = $latestId ? $latestId + 1 : 1;
                $order->order_id = 'ORD' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Get the order items for this order.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}

