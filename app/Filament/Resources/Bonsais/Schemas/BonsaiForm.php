<?php

namespace App\Filament\Resources\Bonsais\Schemas;

use App\Models\BonsaiType;
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
                    ->content(fn ($record): string => $record?->bonsaiType?->name ?? '-'),

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

                Select::make('size')
                    ->options([
                        'Small' => 'Small',
                        'Medium' => 'Medium',
                        'Large' => 'Large',
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
                        $url = $media?->getAvailableUrl(['optimized']);

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
                    ->conversion('optimized')
                    ->maxSize(51200)
                    ->helperText('Foto asli tetap disimpan. Versi tampilan dikompres JPEG kualitas tinggi tanpa mengubah resolusi.')
                    ->extraInputAttributes([
                        'capture' => 'environment',
                    ])
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
