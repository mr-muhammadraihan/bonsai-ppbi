<?php

namespace App\Filament\Resources\Bonsais\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class BonsaiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('participant_id')
                    ->relationship('participant', 'name')
                    ->required()
                    ->preload(),

                Placeholder::make('bonsai_type_name')
                    ->label('ID Bonsai')
                    ->content(fn ($record): string => $record?->bonsai_type ?: $record?->bonsaiType?->name ?? '-'),

                TextInput::make('bonsai_type')
                    ->label('Jenis Bonsai')
                    ->maxLength(255)
                    ->required(),

                Select::make('size')
                    ->options([
                        'Small' => 'Small',
                        'Medium' => 'Medium',
                        'Large' => 'Large',
                        'Mame' => 'Mame',
                    ])
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
                    ->columnSpanFull()
                    ->nullable(),

                Placeholder::make('photo')
                    ->label('Foto Bonsai Saat Ini')
                    ->content(function ($record) {
                        $media = $record?->getPhotoMedia();
                        $url = $media?->getUrl();

                        if (! $url && $record?->photo) {
                            $url = Storage::disk('public')->url($record->photo);
                        }

                        return $url
                            ? new HtmlString(
                                '<img src="'.e($url).'"
                                    style="width: 250px; border-radius: 8px;">'
                            )
                            : 'Belum ada foto';
                    }),

                SpatieMediaLibraryFileUpload::make('photo')
                    ->label('Foto Bonsai')
                    ->collection('bonsai-photos')
                    ->image()
                    ->maxSize(51200)
                    ->helperText('Foto dikompres maksimal 1 MB dengan resolusi dipertahankan selama memungkinkan.')
                    ->extraInputAttributes([
                        'capture' => 'environment',
                    ])
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
