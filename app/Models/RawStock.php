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
    
    /**
     * Relasi many-to-many ke Production melalui pivot table production_materials
     */
    public function productions()
    {
        return $this->belongsToMany(Production::class, 'production_materials', 'material_id', 'production_id')
                    ->withPivot('material_qty')
                    ->withTimestamps();
    }
    
    /**
     * Mengurangi quantity raw stock
     * 
     * @param int $qty Jumlah yang akan dikurangi
     * @return bool
     * @throws \Exception Jika qty tidak cukup
     */
    public function reduceQty($qty)
    {
        if ($this->material_qty < $qty) {
            throw new \Exception("Stok tidak cukup. Stok tersedia: {$this->material_qty}, dibutuhkan: {$qty}");
        }
        
        $this->material_qty -= $qty;
        $this->total_price = $this->material_qty * $this->unit_price;
        return $this->save();
    }
    
    /**
     * Menambah quantity raw stock
     * 
     * @param int $qty Jumlah yang akan ditambahkan
     * @return bool
     */
    public function addQty($qty)
    {
        $this->material_qty += $qty;
        $this->total_price = $this->material_qty * $this->unit_price;
        return $this->save();
    }
}
