<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use App\Models\BonsaiType;
use App\Models\Participant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function create(): View
    {
        return view('registration.create', [
            'bonsaiTypes' => BonsaiType::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreRegistrationRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $participant = DB::transaction(function () use ($validated): Participant {
            $participant = Participant::create(Arr::only($validated, ['name', 'email', 'no_hp']));

            foreach ($validated['bonsais'] as $bonsaiData) {
                $photo = $bonsaiData['photo'];
                $bonsai = $participant->bonsais()->create([
                    ...Arr::except($bonsaiData, ['photo']),
                    'photo' => null,
                ]);

                $bonsai->addMedia($photo)->toMediaCollection('bonsai-photos');
                $bonsai->load('participant')->syncPhotoMediaFilename();
            }

            return $participant;
        });

        return redirect()
            ->route('registration.create')
            ->with('registration_success', "Registrasi {$participant->name} berhasil dikirim.");
    }
}
