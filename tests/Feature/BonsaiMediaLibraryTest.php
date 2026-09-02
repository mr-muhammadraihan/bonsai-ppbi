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

it('uses the bonsai type in its generated ID and media filename', function () {
    $participant = Participant::factory()->create([
        'name' => 'Muhammad Raihan',
    ]);

    $bonsai = Bonsai::factory()->create([
        'participant_id' => $participant->id,
        'bonsai_type_id' => BonsaiType::query()->where('name', 'Bonsai Cemara (Juniperus)')->value('id'),
        'photo' => null,
    ]);

    $bonsai->addMedia(UploadedFile::fake()->image('camera-original.jpg'))
        ->toMediaCollection('bonsai-photos');
    $bonsai->load('participant')->syncPhotoMediaFilename();

    $media = $bonsai->getPhotoMedia();

    expect($bonsai->bonsai_code)->toStartWith('BONSAICEMARAJUNIPERUS-')
        ->and($media?->file_name)->toBe('Muhammad Raihan - Bonsai Cemara (Juniperus).jpg')
        ->and($media?->getPathRelativeToRoot())->toBe('bonsais/Muhammad Raihan - Bonsai Cemara (Juniperus).jpg')
        ->and($media?->getPathRelativeToRoot('optimized'))->toStartWith('bonsais/');

    Storage::disk('public')->assertExists('bonsais/Muhammad Raihan - Bonsai Cemara (Juniperus).jpg');
    Storage::disk('public')->assertExists($media->getPathRelativeToRoot('optimized'));
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
