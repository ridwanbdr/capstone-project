<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawStockTransaction extends Model
{
    protected $table = 'raw_stock_transactions';

    protected $fillable = [
        'material_id',
        'material_name',
        'qty',
        'satuan',
        'unit_price',
        'total_price',
        'added_on',
    ];

    protected $casts = [
        'added_on' => 'date',
        'qty' => 'integer',
        'unit_price' => 'integer',
        'total_price' => 'integer',
    ];

    public function rawStock()
    {
        return $this->belongsTo(RawStock::class, 'material_id', 'material_id');
    }
}
