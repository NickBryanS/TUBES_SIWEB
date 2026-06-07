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
        'tanggal_kembali_aktual',
        'total_biaya',
        'denda',
        'keterangan_denda',
        'perpanjangan_hari',
        'status_perpanjangan',
        'status_transaksi',
        'metode_pengambilan',
        'siap_kirim',
        'barang_diterima',
        'alamat_pengiriman',
        'nama_penerima',
        'telepon_penerima',
        'foto_ktp',
        'jenis_jaminan',
        'status_jaminan',
        'rekening_pengembalian',
        'bank_pengembalian',
        'atas_nama_pengembalian',
        'jarak_tempuh',
        'ongkos_kirim',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_mulai'          => 'date',
            'tanggal_selesai'        => 'date',
            'tanggal_kembali_aktual' => 'date',
            'total_biaya'            => 'decimal:2',
            'denda'                  => 'decimal:2',
            'ongkos_kirim'           => 'decimal:2',
            'perpanjangan_hari'      => 'integer',
            'jarak_tempuh'           => 'decimal:2',
            'siap_kirim'             => 'boolean',
            'barang_diterima'        => 'boolean',
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
