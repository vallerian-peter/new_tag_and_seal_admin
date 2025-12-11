<?php

namespace App\Filament\Resources\InseminationServices;

use App\Filament\Resources\InseminationServices\Pages\CreateInseminationService;
use App\Filament\Resources\InseminationServices\Pages\EditInseminationService;
use App\Filament\Resources\InseminationServices\Pages\ListInseminationServices;
use App\Filament\Resources\InseminationServices\Schemas\InseminationServiceForm;
use App\Filament\Resources\InseminationServices\Tables\InseminationServicesTable;
use App\Models\InseminationService;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class InseminationServiceResource extends Resource
{
    protected static ?string $model = InseminationService::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Insemination Services';

    protected static ?string $modelLabel = 'Insemination Service';

    protected static ?string $pluralModelLabel = 'Insemination Services';

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return InseminationServiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InseminationServicesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInseminationServices::route('/'),
            'create' => CreateInseminationService::route('/create'),
            'edit' => EditInseminationService::route('/{record}/edit'),
        ];
    }
}

