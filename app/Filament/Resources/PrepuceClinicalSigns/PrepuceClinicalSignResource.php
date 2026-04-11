<?php

namespace App\Filament\Resources\PrepuceClinicalSigns;

use App\Filament\Resources\PrepuceClinicalSigns\Pages\CreatePrepuceClinicalSign;
use App\Filament\Resources\PrepuceClinicalSigns\Pages\EditPrepuceClinicalSign;
use App\Filament\Resources\PrepuceClinicalSigns\Pages\ListPrepuceClinicalSigns;
use App\Filament\Resources\PrepuceClinicalSigns\Schemas\PrepuceClinicalSignForm;
use App\Filament\Resources\PrepuceClinicalSigns\Tables\PrepuceClinicalSignsTable;
use App\Models\PrepuceClinicalSign;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class PrepuceClinicalSignResource extends Resource
{
    protected static ?string $model = PrepuceClinicalSign::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-eye';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Prepuce Clinical Signs';

    protected static ?string $modelLabel = 'Prepuce Clinical Sign';

    protected static ?string $pluralModelLabel = 'Prepuce Clinical Signs';

    protected static ?int $navigationSort = 33;

    public static function form(Schema $schema): Schema
    {
        return PrepuceClinicalSignForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrepuceClinicalSignsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrepuceClinicalSigns::route('/'),
            'create' => CreatePrepuceClinicalSign::route('/create'),
            'edit' => EditPrepuceClinicalSign::route('/{record}/edit'),
        ];
    }
}
