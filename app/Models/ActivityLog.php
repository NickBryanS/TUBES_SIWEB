<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'aksi',
        'deskripsi',
        'target_type',
        'target_id',
        'ip_address',
    ];

    /**
     * Relasi: Log milik satu User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper: Catat aktivitas baru.
     */
    public static function catat(string $aksi, string $deskripsi, ?string $targetType = null, ?int $targetId = null): self
    {
        return static::create([
            'user_id' => auth()->id(),
            'aksi' => $aksi,
            'deskripsi' => $deskripsi,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'ip_address' => request()->ip(),
        ]);
    }
}
