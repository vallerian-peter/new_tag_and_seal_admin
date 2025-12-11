<?php

namespace App\Filament\Resources\SchoolLevels;

use App\Filament\Resources\SchoolLevels\Pages\CreateSchoolLevel;
use App\Filament\Resources\SchoolLevels\Pages\EditSchoolLevel;
use App\Filament\Resources\SchoolLevels\Pages\ListSchoolLevels;
use App\Filament\Resources\SchoolLevels\Schemas\SchoolLevelForm;
use App\Filament\Resources\SchoolLevels\Tables\SchoolLevelsTable;
use App\Models\SchoolLevel;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SchoolLevelResource extends Resource
{
    protected static ?string $model = SchoolLevel::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static UnitEnum|string|null $navigationGroup = 'System & Configuration';

    protected static ?string $navigationLabel = 'School Levels';

    protected static ?string $modelLabel = 'School Level';

    protected static ?string $pluralModelLabel = 'School Levels';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return SchoolLevelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchoolLevelsTable::configure($table);
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
            'index' => ListSchoolLevels::route('/'),
            'create' => CreateSchoolLevel::route('/create'),
            'edit' => EditSchoolLevel::route('/{record}/edit'),
        ];
    }
}
