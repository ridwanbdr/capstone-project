<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawStock extends Model
{
    protected $table = 'raw_stocks';
    
    protected $primaryKey = 'material_id';
    
    public $incrementing = true;
    
    protected $fillable = [
        'material_name',
        'material_qty',
        'satuan',
        'category',
        'unit_price',
        'total_price',
        'added_on',
    ];
    
    protected $casts = [
        'added_on' => 'date',
        'material_qty' => 'integer',
        'satuan' => 'string',
        'category' => 'string',
        'unit_price' => 'integer',
        'total_price' => 'integer',
    ];
}
