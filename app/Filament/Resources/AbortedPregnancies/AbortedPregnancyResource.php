<?php

namespace App\Filament\Resources\AbortedPregnancies;

use App\Filament\Resources\AbortedPregnancies\Pages\CreateAbortedPregnancy;
use App\Filament\Resources\AbortedPregnancies\Pages\EditAbortedPregnancy;
use App\Filament\Resources\AbortedPregnancies\Pages\ListAbortedPregnancies;
use App\Filament\Resources\AbortedPregnancies\Schemas\AbortedPregnancyForm;
use App\Filament\Resources\AbortedPregnancies\Tables\AbortedPregnanciesTable;
use App\Models\AbortedPregnancy;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class AbortedPregnancyResource extends Resource
{
    protected static ?string $model = AbortedPregnancy::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-circle';

    protected static UnitEnum|string|null $navigationGroup = 'Events & Logs';

    protected static ?string $navigationLabel = 'Aborted Pregnancies';

    protected static ?string $modelLabel = 'Aborted Pregnancy';

    protected static ?string $pluralModelLabel = 'Aborted Pregnancies';

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return AbortedPregnancyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AbortedPregnanciesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAbortedPregnancies::route('/'),
            'create' => CreateAbortedPregnancy::route('/create'),
            'edit' => EditAbortedPregnancy::route('/{record}/edit'),
        ];
    }
}

