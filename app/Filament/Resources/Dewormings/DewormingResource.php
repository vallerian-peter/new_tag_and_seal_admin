<?php

namespace App\Filament\Resources\Dewormings;

use App\Filament\Resources\Dewormings\Pages\CreateDeworming;
use App\Filament\Resources\Dewormings\Pages\EditDeworming;
use App\Filament\Resources\Dewormings\Pages\ListDewormings;
use App\Filament\Resources\Dewormings\Schemas\DewormingForm;
use App\Filament\Resources\Dewormings\Tables\DewormingsTable;
use App\Models\Deworming;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DewormingResource extends Resource
{
    protected static ?string $model = Deworming::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static UnitEnum|string|null $navigationGroup = 'Events & Logs';

    protected static ?string $navigationLabel = 'Dewormings';

    protected static ?string $modelLabel = 'Deworming';

    protected static ?string $pluralModelLabel = 'Dewormings';

    protected static ?int $navigationSort = 4; // will adjust later maybe but set accordingly?

    public static function form(Schema $schema): Schema
    {
        return DewormingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DewormingsTable::configure($table);
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
            'index' => ListDewormings::route('/'),
            'create' => CreateDeworming::route('/create'),
            'edit' => EditDeworming::route('/{record}/edit'),
        ];
    }
}


