<?php

namespace App\Filament\Resources\Species;

use App\Filament\Resources\Species\Pages\CreateSpecie;
use App\Filament\Resources\Species\Pages\EditSpecie;
use App\Filament\Resources\Species\Pages\ListSpecies;
use App\Filament\Resources\Species\Schemas\SpecieForm;
use App\Filament\Resources\Species\Tables\SpeciesTable;
use App\Models\Specie;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SpecieResource extends Resource
{
    protected static ?string $model = Specie::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static UnitEnum|string|null $navigationGroup = 'Livestock & Data';

    protected static ?string $navigationLabel = 'Species';

    protected static ?string $modelLabel = 'Species';

    protected static ?string $pluralModelLabel = 'Species';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return SpecieForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SpeciesTable::configure($table);
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
            'index' => ListSpecies::route('/'),
            'create' => CreateSpecie::route('/create'),
            'edit' => EditSpecie::route('/{record}/edit'),
        ];
    }
}
