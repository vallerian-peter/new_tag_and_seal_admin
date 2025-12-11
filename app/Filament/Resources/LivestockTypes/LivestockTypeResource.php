<?php

namespace App\Filament\Resources\LivestockTypes;

use App\Filament\Resources\LivestockTypes\Pages\CreateLivestockType;
use App\Filament\Resources\LivestockTypes\Pages\EditLivestockType;
use App\Filament\Resources\LivestockTypes\Pages\ListLivestockTypes;
use App\Filament\Resources\LivestockTypes\Schemas\LivestockTypeForm;
use App\Filament\Resources\LivestockTypes\Tables\LivestockTypesTable;
use App\Models\LivestockType;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LivestockTypeResource extends Resource
{
    protected static ?string $model = LivestockType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-plus';
    
    protected static UnitEnum|string|null $navigationGroup = 'Livestock & Data';
    
    protected static ?string $navigationLabel = 'Livestock Types';
    
    protected static ?string $modelLabel = 'Livestock Type';
    
    protected static ?string $pluralModelLabel = 'Livestock Types';
    
    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return LivestockTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LivestockTypesTable::configure($table);
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
            'index' => ListLivestockTypes::route('/'),
            'create' => CreateLivestockType::route('/create'),
            'edit' => EditLivestockType::route('/{record}/edit'),
        ];
    }
}
