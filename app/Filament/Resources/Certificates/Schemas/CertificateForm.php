<?php

namespace App\Filament\Resources\Certificates\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CertificateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('bonsai_id')
                    ->relationship('bonsai', 'id')
                    ->required(),
                Select::make('type')
                    ->options(['Peserta' => 'Peserta', 'Pemenang' => 'Pemenang'])
                    ->required(),
                TextInput::make('certificate_number')
                    ->required(),
                TextInput::make('verification_code')
                    ->required(),
                TextInput::make('file_path')
                    ->required(),
            ]);
    }
}
