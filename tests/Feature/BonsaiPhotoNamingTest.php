<?php

use App\Models\Bonsai;
use App\Models\Participant;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

it('names a new bonsai photo using the participant and bonsai type names', function () {
    $photoPath = 'bonsais/original-photo.jpg';
    Storage::disk('public')->put($photoPath, 'photo');

    $bonsai = Bonsai::factory()->create([
        'photo' => $photoPath,
    ]);

    expect($bonsai->fresh()->photo)->toBe('bonsais/'.$bonsai->participant->name.' - '.$bonsai->bonsaiType->name.'.jpg');
    Storage::disk('public')->assertExists($bonsai->photo);
    Storage::disk('public')->assertMissing($photoPath);
});

it('renames a replacement photo using the participant and bonsai type names', function () {
    $participant = Participant::factory()->create();
    $bonsai = Bonsai::factory()->create([
        'participant_id' => $participant->id,
        'photo' => 'bonsais/old-photo.jpg',
    ]);
    Storage::disk('public')->put('bonsais/new-photo.png', 'photo');

    $bonsai->update(['photo' => 'bonsais/new-photo.png']);

    expect($bonsai->fresh()->photo)->toBe('bonsais/'.$participant->name.' - '.$bonsai->bonsaiType->name.'.png');
    Storage::disk('public')->assertExists($bonsai->photo);
    Storage::disk('public')->assertMissing('bonsais/new-photo.png');
});
