<?php

namespace App\Filament\Resources\Bonsais;

use App\Filament\Resources\Bonsais\Pages\CreateBonsai;
use App\Filament\Resources\Bonsais\Pages\EditBonsai;
use App\Filament\Resources\Bonsais\Pages\ListBonsais;
use App\Filament\Resources\Bonsais\Schemas\BonsaiForm;
use App\Filament\Resources\Bonsais\Tables\BonsaisTable;
use App\Models\Bonsai;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BonsaiResource extends Resource
{
    protected static ?string $model = Bonsai::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Sparkles;

    protected static string|UnitEnum|null $navigationGroup = 'Bonsai'; // Group Bonsai

    protected static ?string $recordTitleAttribute = 'Bonsai';

    /**
     * Get the navigation badge tooltip for the resource.
     */
    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'The number of Bonsai';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Bonsai::count();
    }

    public static function form(Schema $schema): Schema
    {
        return BonsaiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BonsaisTable::configure($table);
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
            'index' => ListBonsais::route('/'),
            'create' => CreateBonsai::route('/create'),
            'edit' => EditBonsai::route('/{record}/edit'),
        ];
    }
}
