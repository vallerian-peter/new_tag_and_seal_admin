<?php

namespace App\Filament\Resources\Medicines;

use App\Filament\Resources\Medicines\Pages\CreateMedicine;
use App\Filament\Resources\Medicines\Pages\EditMedicine;
use App\Filament\Resources\Medicines\Pages\ListMedicines;
use App\Filament\Resources\Medicines\Schemas\MedicineForm;
use App\Filament\Resources\Medicines\Tables\MedicinesTable;
use App\Models\Medicines;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MedicineResource extends Resource
{
    protected static ?string $model = Medicines::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Medicines';

    protected static ?string $modelLabel = 'Medicine';

    protected static ?string $pluralModelLabel = 'Medicines';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return MedicineForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MedicinesTable::configure($table);
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
            'index' => ListMedicines::route('/'),
            'create' => CreateMedicine::route('/create'),
            'edit' => EditMedicine::route('/{record}/edit'),
        ];
    }
}


