<?php

use App\Models\Bonsai;
use App\Models\Participant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

it('renders the public registration form', function () {
    $response = $this->get('/registrasi');

    $response->assertOk()
        ->assertSee('Data peserta')
        ->assertSee('Data bonsai')
        ->assertSee('Jenis bonsai')
        ->assertSee('Mame')
        ->assertSee('Tambah bonsai')
        ->assertDontSee('Nomor WhatsApp')
        ->assertDontSee('Email')
        ->assertSee('capture="environment"', false);
});

it('stores a participant and all bonsais submitted in one form', function () {
    $response = $this->post('/registrasi', [
        'name' => 'Muhammad Raihan',
        'bonsais' => [
            [
                'bonsai_type' => 'Bonsai Beringin / Ficus',
                'size' => 'Medium',
                'class' => 'Jadi',
                'status' => 'Peserta',
                'predicate' => null,
                'description' => 'Beringin sehat.',
                'photo' => UploadedFile::fake()->image('beringin.jpg'),
            ],
            [
                'bonsai_type' => 'Bonsai koleksi baru',
                'size' => 'Mame',
                'class' => 'Prospek',
                'status' => 'Peserta',
                'predicate' => null,
                'description' => null,
                'photo' => UploadedFile::fake()->image('koleksi-baru.jpg'),
            ],
        ],
    ]);

    $participant = Participant::query()->where('name', 'Muhammad Raihan')->firstOrFail();

    $response->assertRedirect(route('registration.create'));
    expect($participant->bonsais)->toHaveCount(2);
    expect(Bonsai::query()->where('participant_id', $participant->id)->pluck('bonsai_type')->all())
        ->toContain('Bonsai koleksi baru');
    expect(Bonsai::query()->where('participant_id', $participant->id)->pluck('size')->all())
        ->toContain('Mame');
    Storage::disk('public')->assertExists('bonsais/Muhammad Raihan - Bonsai Beringin - Ficus.jpg');
});

it('creates a new bonsai type from the public modal endpoint', function () {
    $response = $this->postJson('/bonsai-types', [
        'name' => 'Bonsai Kemuning',
    ]);

    $response->assertCreated()
        ->assertJsonPath('name', 'Bonsai Kemuning');
    $this->assertDatabaseHas('bonsai_types', ['name' => 'Bonsai Kemuning']);
});

it('rejects a registration without a bonsai photo', function () {
    $response = $this->from('/registrasi')->post('/registrasi', [
        'name' => 'Muhammad Raihan',
        'bonsais' => [[
            'bonsai_type' => 'Bonsai Beringin / Ficus',
            'size' => 'Medium',
            'class' => 'Jadi',
            'status' => 'Peserta',
        ]],
    ]);

    $response->assertRedirect('/registrasi')
        ->assertSessionHasErrors('bonsais.0.photo');
    expect(Participant::query()->where('name', 'Muhammad Raihan')->exists())->toBeFalse();
});
