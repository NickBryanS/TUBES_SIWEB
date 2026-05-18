<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Address;
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
        'foto_profil',
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
     * Helper: cek apakah user adalah admin.
     */
    public function isAdmin(): bool
    {
        return $this->peran === 'admin';
    }

    /**
     * Helper: cek apakah user adalah super admin.
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

    /**
     * Relasi: User memiliki banyak Address.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Helper: ambil foto profil URL.
     */
    public function getFotoProfilUrl(): string
    {
        if ($this->foto_profil) {
            return asset('uploads/profil/' . $this->foto_profil);
        }
        if ($this->avatar) {
            return $this->avatar;
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nama_lengkap) . '&background=2D5A27&color=fff&size=200';
    }
}
