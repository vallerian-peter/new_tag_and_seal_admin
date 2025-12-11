<?php

namespace App\Filament\Resources\Milkings;

use App\Filament\Resources\Milkings\Pages\CreateMilking;
use App\Filament\Resources\Milkings\Pages\EditMilking;
use App\Filament\Resources\Milkings\Pages\ListMilkings;
use App\Filament\Resources\Milkings\Schemas\MilkingForm;
use App\Filament\Resources\Milkings\Tables\MilkingsTable;
use App\Models\Milking;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class MilkingResource extends Resource
{
    protected static ?string $model = Milking::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected static UnitEnum|string|null $navigationGroup = 'Events & Logs';

    protected static ?string $navigationLabel = 'Milking';

    protected static ?string $modelLabel = 'Milking';

    protected static ?string $pluralModelLabel = 'Milking Logs';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return MilkingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MilkingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMilkings::route('/'),
            'create' => CreateMilking::route('/create'),
            'edit' => EditMilking::route('/{record}/edit'),
        ];
    }
}

