<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'detail_id',
        'pesanan_id',
        'user_id',
        'nama_produk',
        'jenis_kain',
        'warna',
        'ukuran',
        'jumlah'
    ];
    
    /**
     * Get the OrderMassal that owns this detail.
     */
    public function orderMassal(): BelongsTo
    {
        return $this->belongsTo(OrderMassal::class, 'pesanan_id');
    }
    
    /**
     * Get the User that created this detail.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}