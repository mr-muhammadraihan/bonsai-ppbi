<?php

namespace App\Filament\Resources\Participants;

use App\Filament\Resources\Participants\Pages\CreateParticipant;
use App\Filament\Resources\Participants\Pages\EditParticipant;
use App\Filament\Resources\Participants\Pages\ListParticipants;
use App\Filament\Resources\Participants\Schemas\ParticipantForm;
use App\Filament\Resources\Participants\Tables\ParticipantsTable;
use App\Models\Participant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ParticipantResource extends Resource
{
    protected static ?string $model = Participant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Participants'; // Group Peserta

    protected static ?string $recordTitleAttribute = 'Participant';

    /**
     * Get the navigation badge tooltip for the resource.
     */
    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'The number of Participants';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Participant::count();
    }

    public static function form(Schema $schema): Schema
    {
        return ParticipantForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ParticipantsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListParticipants::route('/'),
            'create' => CreateParticipant::route('/create'),
            'edit' => EditParticipant::route('/{record}/edit'),
        ];
    }
}
