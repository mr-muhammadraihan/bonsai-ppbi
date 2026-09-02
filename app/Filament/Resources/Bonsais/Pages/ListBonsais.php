<?php

namespace App\Filament\Resources\Bonsais\Pages;

use App\Filament\Resources\Bonsais\BonsaiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBonsais extends ListRecords
{
    protected static string $resource = BonsaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
