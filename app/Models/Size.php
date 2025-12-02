<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $table = 'size';
    protected $primaryKey = 'size_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'size_label',
        'width',
        'height',
        'sleeve',
    ];

    protected $casts = [
        'size_id' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'sleeve' => 'integer',
    ];

    public function detailProducts()
    {
        return $this->hasMany(DetailProduct::class, 'size_id', 'size_id');
    }
}
