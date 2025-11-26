<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    protected $table = 'productions';
    
    protected $primaryKey = 'production_id';
    
    public $incrementing = true;
    
    protected $fillable = [
        'production_lead',
        'production_label',
        'production_date',
        'total_unit'
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
}
