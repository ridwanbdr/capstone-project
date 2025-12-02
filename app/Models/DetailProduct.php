<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Size;
use App\Models\Production;

class DetailProduct extends Model
{
    protected $table = 'detail_product';
    protected $primaryKey = 'product_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'production_id',
        'product_name',
        'size_id',
        'qty_unit',
        'price_unit',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'production_id' => 'integer',
        'size_id' => 'integer',
        'qty_unit' => 'integer',
        'price_unit' => 'integer',
    ];

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id', 'size_id');
    }

    // relasi ke Production (many-to-one)
    public function production()
    {
        return $this->belongsTo(Production::class, 'production_id', 'production_id');
    }
}
