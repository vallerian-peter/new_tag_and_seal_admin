<?php

namespace App\Filament\Resources\BirthEvents;

use App\Filament\Resources\BirthEvents\Pages\CreateBirthEvent;
use App\Filament\Resources\BirthEvents\Pages\EditBirthEvent;
use App\Filament\Resources\BirthEvents\Pages\ListBirthEvents;
use App\Filament\Resources\BirthEvents\Schemas\BirthEventForm;
use App\Filament\Resources\BirthEvents\Tables\BirthEventsTable;
use App\Models\BirthEvent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class BirthEventResource extends Resource
{
    protected static ?string $model = BirthEvent::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document';

    protected static UnitEnum|string|null $navigationGroup = 'Events & Logs';

    protected static ?string $navigationLabel = 'Birth Events';

    protected static ?string $modelLabel = 'Birth Event';

    protected static ?string $pluralModelLabel = 'Birth Events';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return BirthEventForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BirthEventsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBirthEvents::route('/'),
            'create' => CreateBirthEvent::route('/create'),
            'edit' => EditBirthEvent::route('/{record}/edit'),
        ];
    }
}

