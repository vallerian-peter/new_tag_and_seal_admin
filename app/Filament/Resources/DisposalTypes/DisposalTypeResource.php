<?php

namespace App\Filament\Resources\DisposalTypes;

use App\Filament\Resources\DisposalTypes\Pages\CreateDisposalType;
use App\Filament\Resources\DisposalTypes\Pages\EditDisposalType;
use App\Filament\Resources\DisposalTypes\Pages\ListDisposalTypes;
use App\Filament\Resources\DisposalTypes\Schemas\DisposalTypeForm;
use App\Filament\Resources\DisposalTypes\Tables\DisposalTypesTable;
use App\Models\DisposalType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class DisposalTypeResource extends Resource
{
    protected static ?string $model = DisposalType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-trash';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Disposal Types';

    protected static ?string $modelLabel = 'Disposal Type';

    protected static ?string $pluralModelLabel = 'Disposal Types';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return DisposalTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DisposalTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDisposalTypes::route('/'),
            'create' => CreateDisposalType::route('/create'),
            'edit' => EditDisposalType::route('/{record}/edit'),
        ];
    }
}

