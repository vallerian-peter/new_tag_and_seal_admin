<?php

namespace App\Filament\Resources\Pregnancies;

use App\Filament\Resources\Pregnancies\Pages\CreatePregnancy;
use App\Filament\Resources\Pregnancies\Pages\EditPregnancy;
use App\Filament\Resources\Pregnancies\Pages\ListPregnancies;
use App\Filament\Resources\Pregnancies\Schemas\PregnancyForm;
use App\Filament\Resources\Pregnancies\Tables\PregnanciesTable;
use App\Models\Pregnancy;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class PregnancyResource extends Resource
{
    protected static ?string $model = Pregnancy::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static UnitEnum|string|null $navigationGroup = 'Events & Logs';

    protected static ?string $navigationLabel = 'Pregnancy';

    protected static ?string $modelLabel = 'Pregnancy';

    protected static ?string $pluralModelLabel = 'Pregnancy Logs';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return PregnancyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PregnanciesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPregnancies::route('/'),
            'create' => CreatePregnancy::route('/create'),
            'edit' => EditPregnancy::route('/{record}/edit'),
        ];
    }
}

