<?php

namespace App\Filament\Resources\IdentificationEvents;

use App\Filament\Resources\IdentificationEvents\Pages\CreateIdentificationEvent;
use App\Filament\Resources\IdentificationEvents\Pages\EditIdentificationEvent;
use App\Filament\Resources\IdentificationEvents\Pages\ListIdentificationEvents;
use App\Filament\Resources\IdentificationEvents\Schemas\IdentificationEventForm;
use App\Filament\Resources\IdentificationEvents\Tables\IdentificationEventsTable;
use App\Models\IdentificationEvent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class IdentificationEventResource extends Resource
{
    protected static ?string $model = IdentificationEvent::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-identification';
    protected static UnitEnum|string|null $navigationGroup = 'Events & Logs';
    protected static ?string $navigationLabel = 'Identification Events';
    protected static ?string $modelLabel = 'Identification Event';
    protected static ?string $pluralModelLabel = 'Identification Events';
    protected static ?int $navigationSort = 24;

    public static function form(Schema $schema): Schema
    {
        return IdentificationEventForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IdentificationEventsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIdentificationEvents::route('/'),
            'create' => CreateIdentificationEvent::route('/create'),
            'edit' => EditIdentificationEvent::route('/{record}/edit'),
        ];
    }
}

