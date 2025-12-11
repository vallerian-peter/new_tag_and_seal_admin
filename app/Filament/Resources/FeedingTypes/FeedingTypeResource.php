<?php

namespace App\Filament\Resources\FeedingTypes;

use App\Filament\Resources\FeedingTypes\Pages\CreateFeedingType;
use App\Filament\Resources\FeedingTypes\Pages\EditFeedingType;
use App\Filament\Resources\FeedingTypes\Pages\ListFeedingTypes;
use App\Filament\Resources\FeedingTypes\Schemas\FeedingTypeForm;
use App\Filament\Resources\FeedingTypes\Tables\FeedingTypesTable;
use App\Models\FeedingType;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FeedingTypeResource extends Resource
{
    protected static ?string $model = FeedingType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cake';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Feeding Types';

    protected static ?string $modelLabel = 'Feeding Type';

    protected static ?string $pluralModelLabel = 'Feeding Types';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return FeedingTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FeedingTypesTable::configure($table);
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
            'index' => ListFeedingTypes::route('/'),
            'create' => CreateFeedingType::route('/create'),
            'edit' => EditFeedingType::route('/{record}/edit'),
        ];
    }
}


