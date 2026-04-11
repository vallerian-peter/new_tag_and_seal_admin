<?php

namespace App\Filament\Resources\IronInjections;

use App\Filament\Resources\IronInjections\Pages\CreateIronInjection;
use App\Filament\Resources\IronInjections\Pages\EditIronInjection;
use App\Filament\Resources\IronInjections\Pages\ListIronInjections;
use App\Filament\Resources\IronInjections\Schemas\IronInjectionForm;
use App\Filament\Resources\IronInjections\Tables\IronInjectionsTable;
use App\Models\IronInjection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class IronInjectionResource extends Resource
{
    protected static ?string $model = IronInjection::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-beaker';
    protected static UnitEnum|string|null $navigationGroup = 'Events & Logs';
    protected static ?string $navigationLabel = 'Iron Injections';
    protected static ?string $modelLabel = 'Iron Injection';
    protected static ?string $pluralModelLabel = 'Iron Injections';
    protected static ?int $navigationSort = 21;

    public static function form(Schema $schema): Schema
    {
        return IronInjectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IronInjectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIronInjections::route('/'),
            'create' => CreateIronInjection::route('/create'),
            'edit' => EditIronInjection::route('/{record}/edit'),
        ];
    }
}

