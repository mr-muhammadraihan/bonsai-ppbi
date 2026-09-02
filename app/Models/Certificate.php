<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'bonsai_id',
        'type',
        'certificate_number',
        'verification_code',
        'file_path',
    ];

    /**
     * Relasi: Certificate dimiliki oleh satu Bonsai.
     */
    public function bonsai(): BelongsTo
    {
        return $this->belongsTo(Bonsai::class);
    }

    /**
     * Mengecek apakah sertifikat adalah Sertifikat Peserta.
     */
    public function isParticipant(): bool
    {
        return $this->type === 'Peserta';
    }

    /**
     * Mengecek apakah sertifikat adalah Sertifikat Pemenang.
     */
    public function isWinner(): bool
    {
        return $this->type === 'Pemenang';
    }
}
