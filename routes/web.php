<?php

use App\Http\Controllers\BonsaiTypeController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RegistrationController::class, 'create']);
Route::get('/registrasi', [RegistrationController::class, 'create'])
    ->name('registration.create');
Route::post('/registrasi', [RegistrationController::class, 'store'])
    ->name('registration.store');
Route::post('/bonsai-types', [BonsaiTypeController::class, 'store'])
    ->name('bonsai-types.store');
