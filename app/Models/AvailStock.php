<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\DetailProduct;
use App\Models\Size;
use App\Models\Transaction;

class AvailStock extends Model
{
    protected $table = 'avail_stocks';

    protected $fillable = [
        'product_name',
        'size_id',
        'qty_unit',
        'price_unit',
    ];

    protected $casts = [
        'id' => 'integer',
        'product_name' => 'string',
        'size_id' => 'integer',
        'qty_unit' => 'integer',
        'price_unit' => 'integer',
    ];

    /**
     * AvailStock belongs to Size (foreign key avail_stocks.size_id -> size.size_id)
     */
    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class, 'size_id', 'size_id');
    }

    /**
     * Transactions linked to this avail stock.
     * Note: transactions table uses column named 'id' as FK referencing avail_stocks.id
     * so foreignKey on Transaction model is 'id' and local key here is 'id'.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'id', 'id');
    }

}
