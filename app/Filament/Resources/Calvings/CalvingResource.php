<?php

namespace App\Filament\Resources\Calvings;

use App\Filament\Resources\Calvings\Pages\CreateCalving;
use App\Filament\Resources\Calvings\Pages\EditCalving;
use App\Filament\Resources\Calvings\Pages\ListCalvings;
use App\Filament\Resources\Calvings\Schemas\CalvingForm;
use App\Filament\Resources\Calvings\Tables\CalvingsTable;
use App\Models\Calving;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class CalvingResource extends Resource
{
    protected static ?string $model = Calving::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document';

    protected static UnitEnum|string|null $navigationGroup = 'Events & Logs';

    protected static ?string $navigationLabel = 'Calving';

    protected static ?string $modelLabel = 'Calving';

    protected static ?string $pluralModelLabel = 'Calving Logs';

    protected static ?int $navigationSort = 7;

    // Hide from navigation - use BirthEvents instead
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return CalvingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CalvingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCalvings::route('/'),
            'create' => CreateCalving::route('/create'),
            'edit' => EditCalving::route('/{record}/edit'),
        ];
    }
}

