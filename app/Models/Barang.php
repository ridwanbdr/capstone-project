<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'nama_barang',
        'jenis_barang',
        'stok',
        'satuan',
        'harga_satuan'
    ];
    
    /**
     * Get the BarangMasuk records for this barang.
     */
    public function barangMasuk(): HasMany
    {
        return $this->hasMany(BarangMasuk::class, 'barang_id');
    }
    
    /**
     * Get the BarangKeluar records for this barang.
     */
    public function barangKeluar(): HasMany
    {
        return $this->hasMany(BarangKeluar::class, 'barang_id');
    }
    
    /**
     * Get the QualityCheck records for this barang.
     */
    public function qualityCheck(): HasMany
    {
        return $this->hasMany(QualityCheck::class, 'barang_id');
    }
    
    /**
     * Get the Transaksi records for this barang.
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'barang_id');
    }
}
