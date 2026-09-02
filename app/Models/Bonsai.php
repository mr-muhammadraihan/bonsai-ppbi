<?php

namespace App\Models;

use App\BonsaiStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Bonsai extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'participant_id',
        'bonsai_type_id',
        'bonsai_type',
        'bonsai_code',
        'size',
        'class',
        'status',
        'photo',
        'predicate',
        'description',
    ];

    /**
     * Relasi: Bonsai dimiliki oleh satu Participant.
     */
    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function bonsaiType(): BelongsTo
    {
        return $this->belongsTo(BonsaiType::class);
    }

    /**
     * Relasi: Bonsai memiliki banyak Certificate.
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Mendapatkan Sertifikat Peserta.
     */
    public function participantCertificate()
    {
        return $this->certificates()
            ->where('type', 'Peserta')
            ->first();
    }

    /**
     * Mendapatkan Sertifikat Pemenang.
     */
    public function winnerCertificate()
    {
        return $this->certificates()
            ->where('type', 'Pemenang')
            ->first();
    }

    /**
     * Mengecek apakah Bonsai merupakan pemenang.
     */
    public function isWinner()
    {
        return $this->status === BonsaiStatus::PEMENANG;
    }

    /**
     * Mengecek apakah Bonsai memiliki predikat.
     */
    public function hasPredicate(): bool
    {
        return ! empty($this->predicate);
    }

    public function registerMediaCollections(): void
    {
        // $this->addMediaCollection('bonsai-photos')->useDisk('public')->singleFile();
        $this->addMediaCollection('bonsai-photos')->useDisk('bonsai')->singleFile();
    }

    public function getPhotoMedia(): ?Media
    {
        return $this->getFirstMedia('bonsai-photos');
    }

    public function downloadPhoto(): ?StreamedResponse
    {
        $media = $this->getPhotoMedia();
        $path = $media?->getPathRelativeToRoot() ?? $this->photo;

        if (blank($path)) {
            return null;
        }

        $disk = $media?->disk ?? 'public';
        $filename = $media?->file_name ?? basename($path);

        return Storage::disk($disk)->download($path, $filename);
    }

    public function syncPhotoMediaFilename(): void
    {
        $media = $this->getPhotoMedia();

        if (! $media) {
            return;
        }

        $filename = $this->photoFilename($media->extension);
        $currentPath = $media->getPathRelativeToRoot();
        $currentConversionPath = $media->hasGeneratedConversion('optimized')
            ? $media->getPathRelativeToRoot('optimized')
            : null;
        $newPath = 'bonsais/'.$filename;

        if ($currentPath !== $newPath) {
            Storage::disk($media->disk)->move($currentPath, $newPath);
            $media->file_name = $filename;
            $media->saveQuietly();
        }

        if ($currentConversionPath) {
            $newConversionPath = $media->getPathRelativeToRoot('optimized');

            if ($currentConversionPath !== $newConversionPath) {
                Storage::disk($media->conversions_disk)->move($currentConversionPath, $newConversionPath);
            }
        }
    }

    /**
     * Melakukan pembuatan ID Bonsai otomatis
     */
    protected static function booted(): void
    {
        static::creating(function (Bonsai $bonsai) {
            $typeName = $bonsai->bonsai_type
                ?: BonsaiType::query()->whereKey($bonsai->bonsai_type_id)->value('name')
                ?: 'Other';
            $prefix = Str::upper(Str::slug($typeName, ''));

            $lastNumber = static::where('bonsai_code', 'like', "{$prefix}-%")
                ->orderByDesc('bonsai_code')
                ->value('bonsai_code');

            $number = $lastNumber
                ? ((int) str($lastNumber)->afterLast('-')->toString()) + 1
                : 1;

            $bonsai->bonsai_code = sprintf(
                '%s-%03d',
                $prefix,
                $number
            );
        });

        static::created(function (Bonsai $bonsai): void {
            $bonsai->syncLegacyPhotoFilename();
        });

        static::updated(function (Bonsai $bonsai): void {
            if ($bonsai->wasChanged('photo')) {
                $bonsai->syncLegacyPhotoFilename($bonsai->getOriginal('photo'));
            }
        });

        parent::boot();

        static::deleting(function (Bonsai $bonsai) {
            // Hapus file foto bonsai jika ada
            if ($bonsai->photo) {
                Storage::disk('public')->delete($bonsai->photo);
            }
        });
    }

    private function syncLegacyPhotoFilename(?string $oldPhoto = null): void
    {
        if (blank($this->photo)) {
            return;
        }

        $extension = pathinfo($this->photo, PATHINFO_EXTENSION);
        $filename = $this->photoFilename($extension);
        $newPath = 'bonsais/'.$filename;

        if ($this->photo !== $newPath && Storage::disk('public')->exists($this->photo)) {
            Storage::disk('public')->move($this->photo, $newPath);
            $this->updateQuietly(['photo' => $newPath]);
        }

        if ($oldPhoto && $oldPhoto !== $newPath) {
            Storage::disk('public')->delete($oldPhoto);
        }
    }

    private function photoFilename(string $extension): string
    {
        $participantName = $this->participant?->name ?? 'Peserta';
        $bonsaiTypeName = $this->bonsai_type
            ?: $this->bonsaiType?->name
            ?: 'Other';
        $name = implode(' - ', [
            $participantName,
            $bonsaiTypeName,
            $this->size ?: 'Unknown',
            $this->class ?: 'Unknown',
            $this->id,
        ]);
        $name = preg_replace('/[\\/:*?"<>|]+/', ' - ', $name) ?? $name;
        $name = Str::squish($name);

        return $name.($extension ? '.'.$extension : '');
    }
}
