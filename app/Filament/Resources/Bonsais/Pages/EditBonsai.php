<?php

namespace App\Filament\Resources\Bonsais\Pages;

use App\Filament\Resources\Bonsais\BonsaiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBonsai extends EditRecord
{
    protected static string $resource = BonsaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->record->load('participant')->syncPhotoMediaFilename();
    }
}
