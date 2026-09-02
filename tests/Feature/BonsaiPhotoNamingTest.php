<?php

use App\Models\Bonsai;
use App\Models\BonsaiType;
use App\Models\Participant;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

it('names a new bonsai photo with the bonsai details and id', function () {
    $photoPath = 'bonsais/original-photo.jpg';
    Storage::disk('public')->put($photoPath, 'photo');

    $bonsai = Bonsai::factory()->create([
        'photo' => $photoPath,
    ]);

    expect($bonsai->fresh()->photo)->toBe('bonsais/'.implode(' - ', [
        $bonsai->participant->name,
        $bonsai->bonsaiType->name,
        $bonsai->size,
        $bonsai->class,
        $bonsai->id,
    ]).'.jpg');
    Storage::disk('public')->assertExists($bonsai->photo);
    Storage::disk('public')->assertMissing($photoPath);
});

it('renames a replacement photo with the bonsai details and id', function () {
    $participant = Participant::factory()->create();
    $bonsai = Bonsai::factory()->create([
        'participant_id' => $participant->id,
        'photo' => 'bonsais/old-photo.jpg',
    ]);
    Storage::disk('public')->put('bonsais/new-photo.png', 'photo');

    $bonsai->update(['photo' => 'bonsais/new-photo.png']);

    expect($bonsai->fresh()->photo)->toBe('bonsais/'.implode(' - ', [
        $participant->name,
        $bonsai->bonsaiType->name,
        $bonsai->size,
        $bonsai->class,
        $bonsai->id,
    ]).'.png');
    Storage::disk('public')->assertExists($bonsai->photo);
    Storage::disk('public')->assertMissing('bonsais/new-photo.png');
});

it('keeps photos unique when bonsai details are identical', function () {
    $participant = Participant::factory()->create([
        'name' => 'Muhammad Raihan',
    ]);
    $bonsaiType = BonsaiType::query()->where('name', 'Bonsai Beringin / Ficus')->firstOrFail();
    Storage::disk('public')->put('bonsais/first-photo.jpg', 'photo');
    Storage::disk('public')->put('bonsais/second-photo.jpg', 'photo');

    $firstBonsai = Bonsai::factory()->create([
        'participant_id' => $participant->id,
        'bonsai_type_id' => $bonsaiType->id,
        'size' => 'Mame',
        'class' => 'Jadi',
        'photo' => 'bonsais/first-photo.jpg',
    ]);
    $secondBonsai = Bonsai::factory()->create([
        'participant_id' => $participant->id,
        'bonsai_type_id' => $bonsaiType->id,
        'size' => 'Mame',
        'class' => 'Jadi',
        'photo' => 'bonsais/second-photo.jpg',
    ]);

    expect($firstBonsai->fresh()->photo)
        ->not->toBe($secondBonsai->fresh()->photo)
        ->and($firstBonsai->fresh()->photo)->toContain('- '.$firstBonsai->id.'.jpg')
        ->and($secondBonsai->fresh()->photo)->toContain('- '.$secondBonsai->id.'.jpg');
});
