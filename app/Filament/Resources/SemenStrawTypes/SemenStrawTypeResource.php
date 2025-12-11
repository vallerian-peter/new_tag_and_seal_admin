<?php

namespace App\Filament\Resources\SemenStrawTypes;

use App\Filament\Resources\SemenStrawTypes\Pages\CreateSemenStrawType;
use App\Filament\Resources\SemenStrawTypes\Pages\EditSemenStrawType;
use App\Filament\Resources\SemenStrawTypes\Pages\ListSemenStrawTypes;
use App\Filament\Resources\SemenStrawTypes\Schemas\SemenStrawTypeForm;
use App\Filament\Resources\SemenStrawTypes\Tables\SemenStrawTypesTable;
use App\Models\SemenStrawType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class SemenStrawTypeResource extends Resource
{
    protected static ?string $model = SemenStrawType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Semen Straw Types';

    protected static ?string $modelLabel = 'Semen Straw Type';

    protected static ?string $pluralModelLabel = 'Semen Straw Types';

    protected static ?int $navigationSort = 9;

    public static function form(Schema $schema): Schema
    {
        return SemenStrawTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SemenStrawTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSemenStrawTypes::route('/'),
            'create' => CreateSemenStrawType::route('/create'),
            'edit' => EditSemenStrawType::route('/{record}/edit'),
        ];
    }
}

