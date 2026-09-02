<?php

namespace App\Providers;

use App\Services\BonsaiPhotoCompressor;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(MediaHasBeenAddedEvent::class, [BonsaiPhotoCompressor::class, 'handle']);
    }
}
