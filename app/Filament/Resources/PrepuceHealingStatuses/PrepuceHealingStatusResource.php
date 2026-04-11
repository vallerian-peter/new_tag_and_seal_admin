<?php

namespace App\Filament\Resources\PrepuceHealingStatuses;

use App\Filament\Resources\PrepuceHealingStatuses\Pages\CreatePrepuceHealingStatus;
use App\Filament\Resources\PrepuceHealingStatuses\Pages\EditPrepuceHealingStatus;
use App\Filament\Resources\PrepuceHealingStatuses\Pages\ListPrepuceHealingStatuses;
use App\Filament\Resources\PrepuceHealingStatuses\Schemas\PrepuceHealingStatusForm;
use App\Filament\Resources\PrepuceHealingStatuses\Tables\PrepuceHealingStatusesTable;
use App\Models\PrepuceHealingStatus;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class PrepuceHealingStatusResource extends Resource
{
    protected static ?string $model = PrepuceHealingStatus::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Prepuce Healing Statuses';

    protected static ?string $modelLabel = 'Prepuce Healing Status';

    protected static ?string $pluralModelLabel = 'Prepuce Healing Statuses';

    protected static ?int $navigationSort = 37;

    public static function form(Schema $schema): Schema
    {
        return PrepuceHealingStatusForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrepuceHealingStatusesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrepuceHealingStatuses::route('/'),
            'create' => CreatePrepuceHealingStatus::route('/create'),
            'edit' => EditPrepuceHealingStatus::route('/{record}/edit'),
        ];
    }
}
