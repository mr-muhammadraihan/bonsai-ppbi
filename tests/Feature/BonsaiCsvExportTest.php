<?php

use App\Models\Bonsai;
use App\Models\Participant;
use App\Services\BonsaiCsvExportService;

it('exports all bonsai data as csv', function () {
    $participant = Participant::factory()->create([
        'name' => 'Muhammad Raihan',
    ]);

    $bonsai = Bonsai::factory()->create([
        'participant_id' => $participant->id,
        'status' => 'Peserta',
    ]);

    $response = (new BonsaiCsvExportService)->downloadAll();

    ob_start();
    $response->sendContent();
    $csv = ob_get_clean();

    expect($csv)
        ->toContain('Nama Peserta')
        ->toContain('Muhammad Raihan')
        ->toContain($bonsai->bonsaiType->name)
        ->toContain('Peserta');
});

it('exports only winner bonsais as csv', function () {
    $participant = Participant::factory()->create();

    $participantBonsai = Bonsai::factory()->create([
        'participant_id' => $participant->id,
        'status' => 'Peserta',
    ]);

    $winnerBonsai = Bonsai::factory()->create([
        'participant_id' => $participant->id,
        'status' => 'Pemenang',
        'predicate' => 'Juara 1',
    ]);

    $response = (new BonsaiCsvExportService)->downloadWinners();

    ob_start();
    $response->sendContent();
    $csv = ob_get_clean();

    expect($csv)
        ->toContain($winnerBonsai->bonsaiType->name)
        ->toContain('Juara 1')
        ->not->toContain($participantBonsai->bonsaiType->name);
});
