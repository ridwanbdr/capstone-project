<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualityCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'quality_check_id',
        'user_id',
        'barang_id',
        'tanggal_check',
        'status',
        'keterangan'
    ];
    
    /**
     * Get the user that performed this quality check.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get the barang for this quality check.
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
