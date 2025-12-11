<?php

namespace App\Filament\Resources\Farmers;

use App\Filament\Resources\Farmers\Pages;
use App\Filament\Resources\Farmers\Pages\CreateFarmer;
use App\Filament\Resources\Farmers\Pages\EditFarmer;
use App\Filament\Resources\Farmers\Pages\ListFarmers;
use App\Filament\Resources\Farmers\Schemas\FarmerForm;
use App\Filament\Resources\Farmers\Tables\FarmersTable;
use App\Models\Farmer;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FarmerResource extends Resource
{
    protected static ?string $model = Farmer::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static UnitEnum|string|null $navigationGroup = 'People & Users';

    protected static ?string $navigationLabel = 'Farmers';

    protected static ?string $modelLabel = 'Farmer';

    protected static ?string $pluralModelLabel = 'Farmers';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return FarmerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FarmersTable::configure($table);
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
            'index' => ListFarmers::route('/'),
            'create' => CreateFarmer::route('/create'),
            'view' => Pages\ViewFarmer::route('/{record}'),
            'edit' => EditFarmer::route('/{record}/edit'),
        ];
    }
}
