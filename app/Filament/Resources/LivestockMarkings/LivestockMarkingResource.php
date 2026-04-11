<?php

namespace App\Filament\Resources\LivestockMarkings;

use App\Filament\Resources\LivestockMarkings\Pages\CreateLivestockMarking;
use App\Filament\Resources\LivestockMarkings\Pages\EditLivestockMarking;
use App\Filament\Resources\LivestockMarkings\Pages\ListLivestockMarkings;
use App\Filament\Resources\LivestockMarkings\Schemas\LivestockMarkingForm;
use App\Filament\Resources\LivestockMarkings\Tables\LivestockMarkingsTable;
use App\Models\LivestockMarking;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class LivestockMarkingResource extends Resource
{
    protected static ?string $model = LivestockMarking::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';
    protected static UnitEnum|string|null $navigationGroup = 'Events & Logs';
    protected static ?string $navigationLabel = 'Livestock Markings';
    protected static ?string $modelLabel = 'Livestock Marking';
    protected static ?string $pluralModelLabel = 'Livestock Markings';
    protected static ?int $navigationSort = 22;

    public static function form(Schema $schema): Schema
    {
        return LivestockMarkingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LivestockMarkingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLivestockMarkings::route('/'),
            'create' => CreateLivestockMarking::route('/create'),
            'edit' => EditLivestockMarking::route('/{record}/edit'),
        ];
    }
}

