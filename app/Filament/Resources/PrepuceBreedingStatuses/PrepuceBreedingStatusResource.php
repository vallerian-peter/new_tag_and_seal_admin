<?php

namespace App\Filament\Resources\PrepuceBreedingStatuses;

use App\Filament\Resources\PrepuceBreedingStatuses\Pages\CreatePrepuceBreedingStatus;
use App\Filament\Resources\PrepuceBreedingStatuses\Pages\EditPrepuceBreedingStatus;
use App\Filament\Resources\PrepuceBreedingStatuses\Pages\ListPrepuceBreedingStatuses;
use App\Filament\Resources\PrepuceBreedingStatuses\Schemas\PrepuceBreedingStatusForm;
use App\Filament\Resources\PrepuceBreedingStatuses\Tables\PrepuceBreedingStatusesTable;
use App\Models\PrepuceBreedingStatus;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class PrepuceBreedingStatusResource extends Resource
{
    protected static ?string $model = PrepuceBreedingStatus::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Prepuce Breeding Statuses';

    protected static ?string $modelLabel = 'Prepuce Breeding Status';

    protected static ?string $pluralModelLabel = 'Prepuce Breeding Statuses';

    protected static ?int $navigationSort = 36;

    public static function form(Schema $schema): Schema
    {
        return PrepuceBreedingStatusForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrepuceBreedingStatusesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrepuceBreedingStatuses::route('/'),
            'create' => CreatePrepuceBreedingStatus::route('/create'),
            'edit' => EditPrepuceBreedingStatus::route('/{record}/edit'),
        ];
    }
}
