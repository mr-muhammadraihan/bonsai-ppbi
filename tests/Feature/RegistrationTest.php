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
        ->assertSee('Shito')
        ->assertSee('Extra Large')
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

    $response->assertRedirect(route('registration.create'));
    $participant = Participant::query()->where('name', 'Muhammad Raihan')->firstOrFail();

    expect($participant->bonsais)->toHaveCount(2);
    expect(Bonsai::query()->where('participant_id', $participant->id)->pluck('bonsai_type')->all())
        ->toContain('Bonsai koleksi baru');
    expect(Bonsai::query()->where('participant_id', $participant->id)->pluck('size')->all())
        ->toContain('Mame');
    $firstBonsai = $participant->bonsais->firstWhere('bonsai_type', 'Bonsai Beringin / Ficus');
    Storage::disk('public')->assertExists($firstBonsai->getPhotoMedia()->getPathRelativeToRoot());
});

it('creates a new bonsai type from the public modal endpoint', function () {
    $response = $this->postJson('/bonsai-types', [
        'name' => 'Bonsai Kemuning',
    ]);

    $response->assertCreated()
        ->assertJsonPath('name', 'Bonsai Kemuning');
    $this->assertDatabaseHas('bonsai_types', ['name' => 'Bonsai Kemuning']);
});

it('accepts Shito and Extra Large bonsai sizes', function () {
    $response = $this->from('/registrasi')->post('/registrasi', [
        'name' => 'Peserta Ukuran Baru',
        'bonsais' => [
            [
                'bonsai_type' => 'Bonsai Shito',
                'size' => 'Shito',
                'class' => 'Jadi',
                'status' => 'Peserta',
            ],
            [
                'bonsai_type' => 'Bonsai Extra Large',
                'size' => 'Extra Large',
                'class' => 'Jadi',
                'status' => 'Peserta',
            ],
        ],
    ]);

    $response->assertRedirect('/registrasi')
        ->assertSessionHasErrors(['bonsais.0.photo', 'bonsais.1.photo'])
        ->assertSessionDoesntHaveErrors(['bonsais.0.size', 'bonsais.1.size']);
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
