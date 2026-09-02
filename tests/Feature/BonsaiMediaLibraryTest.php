<?php

use App\Models\Bonsai;
use App\Models\BonsaiType;
use App\Models\Participant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

beforeEach(function () {
    Storage::fake('public');
});

it('provides the species listed on Bonsai Empire', function () {
    $options = BonsaiType::query()->pluck('name')->all();

    expect($options)->toHaveCount(16)
        ->toContain('Bonsai Beringin / Ficus')
        ->toContain('Lohansung (Podocarpus)')
        ->not->toContain('Maple');
});

it('uses the bonsai type and details in its generated ID and media filename', function () {
    $participant = Participant::factory()->create([
        'name' => 'Muhammad Raihan',
    ]);

    $bonsai = Bonsai::factory()->create([
        'participant_id' => $participant->id,
        'bonsai_type_id' => BonsaiType::query()->where('name', 'Bonsai Cemara (Juniperus)')->value('id'),
        'size' => 'Mame',
        'class' => 'Prospek',
        'photo' => null,
    ]);

    $bonsai->addMedia(UploadedFile::fake()->image('camera-original.jpg'))
        ->toMediaCollection('bonsai-photos');
    $bonsai->load('participant')->syncPhotoMediaFilename();

    $media = $bonsai->getPhotoMedia();

    expect($bonsai->bonsai_code)->toStartWith('BONSAICEMARAJUNIPERUS-')
        ->and($media?->file_name)->toBe('Muhammad Raihan - Bonsai Cemara (Juniperus) - Mame - Prospek - '.$bonsai->id.'.jpg')
        ->and($media?->getPathRelativeToRoot())->toBe('bonsais/Muhammad Raihan - Bonsai Cemara (Juniperus) - Mame - Prospek - '.$bonsai->id.'.jpg')
        ->and($media?->hasGeneratedConversion('optimized'))->toBeFalse();

    Storage::disk('public')->assertExists($media->getPathRelativeToRoot());
});

it('downloads the original media library photo with its filename', function () {
    $bonsai = Bonsai::factory()->create(['photo' => null]);
    $bonsai->addMedia(UploadedFile::fake()->image('bonsai-original.jpg'))
        ->toMediaCollection('bonsai-photos');
    $bonsai->load('participant')->syncPhotoMediaFilename();

    $response = $bonsai->downloadPhoto();

    expect($response)
        ->toBeInstanceOf(StreamedResponse::class)
        ->and($response->headers->get('Content-Disposition'))
        ->toContain($bonsai->getPhotoMedia()->file_name);
});

it('downloads a legacy photo when no media library photo exists', function () {
    $photoPath = 'bonsais/legacy-photo.jpg';
    Storage::disk('public')->put($photoPath, 'photo');
    $bonsai = Bonsai::factory()->create(['photo' => $photoPath]);

    $response = $bonsai->downloadPhoto();

    expect($response)
        ->toBeInstanceOf(StreamedResponse::class)
        ->and($response->headers->get('Content-Disposition'))
        ->toContain(basename($bonsai->fresh()->photo));
});

it('returns no download response when the bonsai has no photo', function () {
    $bonsai = Bonsai::factory()->create(['photo' => null]);

    expect($bonsai->downloadPhoto())->toBeNull();
});

it('allows duplicate bonsai codes without changing existing records', function () {
    $firstBonsai = Bonsai::factory()->create();
    $secondBonsai = Bonsai::factory()->create();
    $originalFirstId = $firstBonsai->id;
    $originalSecondId = $secondBonsai->id;

    $secondBonsai->update(['bonsai_code' => $firstBonsai->bonsai_code]);

    expect(Bonsai::query()->where('bonsai_code', $firstBonsai->bonsai_code)->pluck('id')->all())
        ->toEqualCanonicalizing([$originalFirstId, $originalSecondId]);
});

it('compresses oversized bonsai photos to one megabyte without changing their dimensions', function () {
    $inputPath = tempnam(sys_get_temp_dir(), 'bonsai-photo-');
    $input = imagecreatetruecolor(2400, 2400);
    $colors = [];

    for ($index = 0; $index < 256; $index++) {
        $colors[] = imagecolorallocate($input, $index, ($index * 37) % 256, ($index * 71) % 256);
    }

    for ($y = 0; $y < 2400; $y += 8) {
        for ($x = 0; $x < 2400; $x += 8) {
            imagefilledrectangle($input, $x, $y, $x + 7, $y + 7, $colors[($x * 13 + $y * 7) % 256]);
        }
    }

    imagejpeg($input, $inputPath, 100);
    imagedestroy($input);
    $originalSize = filesize($inputPath);

    $bonsai = Bonsai::factory()->create(['photo' => null]);
    $bonsai->addMedia($inputPath)->toMediaCollection('bonsai-photos');

    if (is_file($inputPath)) {
        unlink($inputPath);
    }

    $media = $bonsai->getPhotoMedia();

    expect($originalSize)->toBeGreaterThan(1_048_576)
        ->and(Storage::disk('public')->size($media->getPathRelativeToRoot()))
        ->toBeLessThanOrEqual(1_048_576)
        ->and(getimagesize($media->getPath())[0])->toBe(2400)
        ->and(getimagesize($media->getPath())[1])->toBe(2400);
});
