<?php

namespace App\Filament\Resources\Participants\Schemas;

use App\Models\BonsaiType;
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
                TextInput::make('no_hp')
                    ->required()
                    ->tel(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),

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

                        Select::make('bonsai_type_id')
                            ->label('Jenis Bonsai')
                            ->relationship('bonsaiType', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Jenis Bonsai Baru')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->createOptionUsing(fn (array $data): int => BonsaiType::create($data)->getKey())
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
