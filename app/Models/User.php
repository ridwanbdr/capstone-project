<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'username',
        'password',
        'role',
        'nama_lengkap',
        'email'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    /**
     * Boot the model and set up the user_id generation.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($user) {
            if (empty($user->user_id)) {
                // Generate a unique user_id, e.g., USR + 6-digit number
                $latestId = self::max('id');
                $nextId = $latestId ? $latestId + 1 : 1;
                $user->user_id = 'USR' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            }
        });
    }
    
    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'user_id';
    }
    
    /**
     * Get the BarangMasuk records for the user.
     */
    public function barangMasuk(): HasMany
    {
        return $this->hasMany(BarangMasuk::class, 'user_id');
    }
    
    /**
     * Get the BarangKeluar records for the user.
     */
    public function barangKeluar(): HasMany
    {
        return $this->hasMany(BarangKeluar::class, 'user_id');
    }
    
    /**
     * Get the QualityCheck records for the user.
     */
    public function qualityCheck(): HasMany
    {
        return $this->hasMany(QualityCheck::class, 'user_id');
    }
    
    /**
     * Get the OrderMassal records for the user.
     */
    public function orderMassal(): HasMany
    {
        return $this->hasMany(OrderMassal::class, 'user_id');
    }
    
    /**
     * Get the Transaksi records for the user.
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'user_id');
    }
}
