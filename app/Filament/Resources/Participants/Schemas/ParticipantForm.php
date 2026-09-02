<?php

namespace App\Filament\Resources\Participants\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ParticipantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Repeater::make('bonsais')
                    ->label('Data Bonsai')
                    ->relationship('bonsais')
                    ->collapsed()
                    ->schema([
                        Select::make('size')
                            ->options([
                                'Small' => 'Small',
                                'Medium' => 'Medium',
                                'Large' => 'Large',
                            ])
                            ->required(),

                        TextInput::make('bonsai_type')
                            ->label('Jenis Bonsai')
                            ->maxLength(255)
                            ->required(),

                        Select::make('class')
                            ->options([
                                'Jadi' => 'Jadi',
                                'Prospek' => 'Prospek',
                            ])
                            ->required(),

                        Select::make('status')
                            ->options([
                                'Peserta' => 'Peserta',
                                'Pemenang' => 'Pemenang',
                            ])
                            ->required(),

                        TextInput::make('predicate')
                            ->nullable(),

                        Textarea::make('description')
                            ->nullable()
                            ->columnSpanFull(),

                        SpatieMediaLibraryFileUpload::make('photo')
                            ->label('Foto Bonsai')
                            ->collection('bonsai-photos')
                            ->image()
                            ->imageEditor(false)
                            ->multiple(false)
                            ->extraInputAttributes([
                                'capture' => 'environment',
                            ])
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
