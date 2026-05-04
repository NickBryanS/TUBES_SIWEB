<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'total_biaya',
        'status_transaksi',
        'metode_pengambilan',
        'alamat_pengiriman',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_mulai'  => 'date',
            'tanggal_selesai' => 'date',
            'total_biaya'    => 'decimal:2',
        ];
    }

    /**
     * Relasi: Transaction milik satu User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Transaction memiliki banyak TransactionDetail.
     */
    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    /**
     * Relasi: Transaction memiliki satu Payment.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
