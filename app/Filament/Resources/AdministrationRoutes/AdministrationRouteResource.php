<?php

namespace App\Filament\Resources\AdministrationRoutes;

use App\Filament\Resources\AdministrationRoutes\Pages\CreateAdministrationRoute;
use App\Filament\Resources\AdministrationRoutes\Pages\EditAdministrationRoute;
use App\Filament\Resources\AdministrationRoutes\Pages\ListAdministrationRoutes;
use App\Filament\Resources\AdministrationRoutes\Schemas\AdministrationRouteForm;
use App\Filament\Resources\AdministrationRoutes\Tables\AdministrationRoutesTable;
use App\Models\AdministrationRoute;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdministrationRouteResource extends Resource
{
    protected static ?string $model = AdministrationRoute::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-uturn-up';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Administration Routes';

    protected static ?string $modelLabel = 'Administration Route';

    protected static ?string $pluralModelLabel = 'Administration Routes';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return AdministrationRouteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdministrationRoutesTable::configure($table);
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
            'index' => ListAdministrationRoutes::route('/'),
            'create' => CreateAdministrationRoute::route('/create'),
            'edit' => EditAdministrationRoute::route('/{record}/edit'),
        ];
    }
}


