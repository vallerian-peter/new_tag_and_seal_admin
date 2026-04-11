<?php

namespace App\Filament\Resources\TeethClippings;

use App\Filament\Resources\TeethClippings\Pages\CreateTeethClipping;
use App\Filament\Resources\TeethClippings\Pages\EditTeethClipping;
use App\Filament\Resources\TeethClippings\Pages\ListTeethClippings;
use App\Filament\Resources\TeethClippings\Schemas\TeethClippingForm;
use App\Filament\Resources\TeethClippings\Tables\TeethClippingsTable;
use App\Models\TeethClipping;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class TeethClippingResource extends Resource
{
    protected static ?string $model = TeethClipping::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-scissors';

    protected static UnitEnum|string|null $navigationGroup = 'Events & Logs';

    protected static ?string $navigationLabel = 'Teeth Clippings';

    protected static ?string $modelLabel = 'Teeth Clipping';

    protected static ?string $pluralModelLabel = 'Teeth Clippings';

    protected static ?int $navigationSort = 19;

    public static function form(Schema $schema): Schema
    {
        return TeethClippingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeethClippingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeethClippings::route('/'),
            'create' => CreateTeethClipping::route('/create'),
            'edit' => EditTeethClipping::route('/{record}/edit'),
        ];
    }
}

