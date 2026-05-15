<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_lengkap',
        'email',
        'nomor_telepon',
        'password',
        'peran',
        'status_verifikasi',
        'dokumen_identitas',
        'status_akun',
        'google_id',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'status_verifikasi' => 'boolean',
        ];
    }

    /**
     * Helper: cek apakah user adalah admin (termasuk superadmin).
     */
    public function isAdmin(): bool
    {
        return in_array($this->peran, ['admin', 'superadmin']);
    }

    /**
     * Helper: cek apakah user adalah superadmin (pemilik).
     */
    public function isSuperAdmin(): bool
    {
        return $this->peran === 'superadmin';
    }

    /**
     * Relasi: User memiliki banyak Transaction.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
