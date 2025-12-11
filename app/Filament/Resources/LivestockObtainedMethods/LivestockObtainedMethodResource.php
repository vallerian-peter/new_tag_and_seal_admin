<?php

namespace App\Filament\Resources\LivestockObtainedMethods;

use App\Filament\Resources\LivestockObtainedMethods\Pages\CreateLivestockObtainedMethod;
use App\Filament\Resources\LivestockObtainedMethods\Pages\EditLivestockObtainedMethod;
use App\Filament\Resources\LivestockObtainedMethods\Pages\ListLivestockObtainedMethods;
use App\Filament\Resources\LivestockObtainedMethods\Schemas\LivestockObtainedMethodForm;
use App\Filament\Resources\LivestockObtainedMethods\Tables\LivestockObtainedMethodsTable;
use App\Models\LivestockObtainedMethod;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LivestockObtainedMethodResource extends Resource
{
    protected static ?string $model = LivestockObtainedMethod::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';
    
    protected static UnitEnum|string|null $navigationGroup = 'Livestock & Data';
    
    protected static ?string $navigationLabel = 'Acquisition Methods';
    
    protected static ?string $modelLabel = 'Livestock Obtained Method';
    
    protected static ?string $pluralModelLabel = 'Livestock Obtained Methods';
    
    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return LivestockObtainedMethodForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LivestockObtainedMethodsTable::configure($table);
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
            'index' => ListLivestockObtainedMethods::route('/'),
            'create' => CreateLivestockObtainedMethod::route('/create'),
            'edit' => EditLivestockObtainedMethod::route('/{record}/edit'),
        ];
    }
}
