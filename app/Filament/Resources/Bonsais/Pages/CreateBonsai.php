<?php

namespace App\Filament\Resources\Bonsais\Pages;

use App\Filament\Resources\Bonsais\BonsaiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBonsai extends CreateRecord
{
    protected static string $resource = BonsaiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $this->record->load('participant')->syncPhotoMediaFilename();
    }
}
