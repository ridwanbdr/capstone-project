<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QcCheck extends Model
{
    /** @use HasFactory<\Database\Factories\QcCheckFactory> */
    use HasFactory;

    protected $table = 'qc_checks';
    protected $primaryKey = 'qc_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'qty_passed',
        'qty_reject',
        'date',
        'qc_checker',
        'qc_label',
        'reject_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'qty_passed' => 'integer',
        'qty_reject' => 'integer',
    ];

    /**
     * Get the detail product associated with this quality check.
     */
    public function detailProduct(): BelongsTo
    {
        return $this->belongsTo(DetailProduct::class, 'product_id', 'product_id');
    }
}
