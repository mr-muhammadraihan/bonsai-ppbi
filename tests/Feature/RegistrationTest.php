<?php

use App\Models\Bonsai;
use App\Models\BonsaiType;
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
        ->assertSee('Tambah bonsai')
        ->assertSee('capture="environment"', false);
});

it('stores a participant and all bonsais submitted in one form', function () {
    $response = $this->post('/registrasi', [
        'name' => 'Muhammad Raihan',
        'email' => 'raihan@example.com',
        'no_hp' => '08123456789',
        'bonsais' => [
            [
                'bonsai_type_id' => BonsaiType::query()->where('name', 'Bonsai Beringin / Ficus')->value('id'),
                'size' => 'Medium',
                'class' => 'Jadi',
                'status' => 'Peserta',
                'predicate' => null,
                'description' => 'Beringin sehat.',
                'photo' => UploadedFile::fake()->image('beringin.jpg'),
            ],
            [
                'bonsai_type_id' => BonsaiType::factory()->create(['name' => 'Bonsai koleksi baru'])->id,
                'size' => 'Small',
                'class' => 'Prospek',
                'status' => 'Peserta',
                'predicate' => null,
                'description' => null,
                'photo' => UploadedFile::fake()->image('koleksi-baru.jpg'),
            ],
        ],
    ]);

    $participant = Participant::query()->where('email', 'raihan@example.com')->firstOrFail();

    $response->assertRedirect(route('registration.create'));
    expect($participant->bonsais)->toHaveCount(2);
    expect(Bonsai::query()->where('participant_id', $participant->id)->with('bonsaiType')->get()->pluck('bonsaiType.name')->all())
        ->toContain('Bonsai koleksi baru');
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
        'email' => 'raihan@example.com',
        'no_hp' => '08123456789',
        'bonsais' => [[
            'bonsai_type_id' => BonsaiType::query()->where('name', 'Bonsai Beringin / Ficus')->value('id'),
            'size' => 'Medium',
            'class' => 'Jadi',
            'status' => 'Peserta',
        ]],
    ]);

    $response->assertRedirect('/registrasi')
        ->assertSessionHasErrors('bonsais.0.photo');
    expect(Participant::query()->where('email', 'raihan@example.com')->exists())->toBeFalse();
});
