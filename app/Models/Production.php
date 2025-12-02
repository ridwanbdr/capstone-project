<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Production extends Model
{
    protected $table = 'productions';
    
    protected $primaryKey = 'production_id';
    
    public $incrementing = true;
    
    protected $fillable = [
        'production_lead',
        'production_label',
        'production_date',
        'total_unit',
        'product_name'
    ];
    
    protected $casts = [
        'production_lead' => 'string',
        'production_label' => 'string',
        'total_unit' => 'integer',
        'production_date' => 'date'
    ];
    
    /**
     * Relasi many-to-many ke RawStock melalui pivot table production_materials
     */
    public function rawStocks()
    {
        return $this->belongsToMany(RawStock::class, 'production_materials', 'production_id', 'material_id')
                    ->withPivot('material_qty')
                    ->withTimestamps();
    }

    /**
     * Get the QcCheck records for this production.
     */
    public function qcChecks(): HasMany
    {
        return $this->hasMany(QcCheck::class, 'product_name', 'product_name');
    }
}

