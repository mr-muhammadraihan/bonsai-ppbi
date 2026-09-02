<?php

namespace App\Filament\Resources\Bonsais\Tables;

use App\Models\Bonsai;
use App\Services\BonsaiCsvExportService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class BonsaisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->imageHeight(300)
                    ->checkFileExistence(false)
                    ->label('Foto Bonsai')
                    ->state(fn (Bonsai $record): ?string => $record->getPhotoMedia()?->getUrl()),
                // ->directory('bonsais'),

                TextColumn::make('bonsai_type')
                    ->label('ID Bonsai')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('participant.name')
                    ->label('Peserta')
                    ->searchable(),

                TextColumn::make('size'),

                TextColumn::make('class'),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('predicate')
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('size')
                    ->options([
                        'Small' => 'Small',
                        'Medium' => 'Medium',
                        'Large' => 'Large',
                        'Mame' => 'Mame',
                    ]),

                SelectFilter::make('class')
                    ->options([
                        'Jadi' => 'Jadi',
                        'Prospek' => 'Prospek',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'Peserta' => 'Peserta',
                        'Pemenang' => 'Pemenang',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),

                Action::make('download_photo')
                    ->label('Download Foto')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('gray')
                    ->action(fn (Bonsai $record) => $record->downloadPhoto())
                    ->visible(fn (Bonsai $record): bool => $record->getPhotoMedia() !== null || filled($record->photo)),

                // Download Participant Certificate
                Action::make('download_participant_certificate')
                    ->label('Download Peserta')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('primary')
                    ->action(function ($record) {
                        $certificate = $record->participantCertificate();

                        if (! $certificate) {
                            return;
                        }

                        return Storage::disk('public')->download($certificate->file_path);
                    })
                    ->visible(fn ($record) => $record->participantCertificate() !== null),

                // Download Winner Certificate
                Action::make('download_winner_certificate')
                    ->label('Download Pemenang')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('success')
                    ->action(function ($record) {
                        $certificate = $record->winnerCertificate();

                        if (! $certificate) {
                            return;
                        }

                        return Storage::disk('public')->download($certificate->file_path);
                    })
                    ->visible(fn ($record) => $record->winnerCertificate() !== null),
            ])
            ->toolbarActions([
                Action::make('export_all_bonsais')
                    ->label('Export Semua CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn () => (new BonsaiCsvExportService)->downloadAll()),

                Action::make('export_winners')
                    ->label('Export Pemenang CSV')
                    ->icon('heroicon-o-trophy')
                    ->color('success')
                    ->action(fn () => (new BonsaiCsvExportService)->downloadWinners()),

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
