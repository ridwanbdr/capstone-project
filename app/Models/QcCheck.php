<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QcCheck extends Model
{
    /** @use HasFactory<\Database\Factories\QcCheckFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'qc_id',
        'product_name',
        'qty_passed',
        'qty_reject',
        'date',
        'qc_checker',
        'qc_label',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the user who performed the quality check.
     */
    public function checker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'qc_checker', 'user_id');
    }

    /**
     * Get the production associated with this quality check.
     */
    public function production(): BelongsTo
    {
        return $this->belongsTo(Production::class, 'product_name', 'product_name');
    }
}
