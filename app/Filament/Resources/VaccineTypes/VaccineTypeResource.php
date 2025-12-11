<?php

namespace App\Filament\Resources\VaccineTypes;

use App\Filament\Resources\VaccineTypes\Pages\CreateVaccineType;
use App\Filament\Resources\VaccineTypes\Pages\EditVaccineType;
use App\Filament\Resources\VaccineTypes\Pages\ListVaccineTypes;
use App\Filament\Resources\VaccineTypes\Schemas\VaccineTypeForm;
use App\Filament\Resources\VaccineTypes\Tables\VaccineTypesTable;
use App\Models\VaccineType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VaccineTypeResource extends Resource
{
    protected static ?string $model = VaccineType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Vaccine Types';

    protected static ?string $modelLabel = 'Vaccine Type';

    protected static ?string $pluralModelLabel = 'Vaccine Types';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return VaccineTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VaccineTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVaccineTypes::route('/'),
            'create' => CreateVaccineType::route('/create'),
            'edit' => EditVaccineType::route('/{record}/edit'),
        ];
    }
}

