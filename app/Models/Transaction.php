<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'transaction_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'date',
        'id', // FK -> avail_stocks.id
        'product_name',
        'size',
        'qty',
        'price',
        'total',
        'paid',
        'payment_method',
        'unpaid_amount',
        'due_date_payment',
        'status',
    ];

    protected $casts = [
        'transaction_id'   => 'integer',
        'id'               => 'integer',
        'qty'              => 'integer',
        'price'            => 'float',
        'total'            => 'float',
        'paid'             => 'float',
        'unpaid_amount'    => 'float',
        'date'             => 'date',
        'due_date_payment' => 'date',
    ];

    /**
     * Belongs to AvailStock (transactions.id -> avail_stocks.id)
     */
    public function availStock(): BelongsTo
    {
        return $this->belongsTo(AvailStock::class, 'id', 'id');
    }
}