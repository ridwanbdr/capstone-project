<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderMassal extends Model
{
    use HasFactory;

    protected $fillable = [
        'pesanan_id',
        'user_id',
        'nama_pemesan',
        'tanggal_pesan',
        'status_pesanan'
    ];
    
    /**
     * Get the details for this order.
     */
    public function details(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'pesanan_id');
    }
    
    /**
     * Get the user that created this order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
