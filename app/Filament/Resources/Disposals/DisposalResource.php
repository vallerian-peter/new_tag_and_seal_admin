<?php

namespace App\Filament\Resources\Disposals;

use App\Filament\Resources\Disposals\Pages\CreateDisposal;
use App\Filament\Resources\Disposals\Pages\EditDisposal;
use App\Filament\Resources\Disposals\Pages\ListDisposals;
use App\Filament\Resources\Disposals\Schemas\DisposalForm;
use App\Filament\Resources\Disposals\Tables\DisposalsTable;
use App\Models\Disposal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class DisposalResource extends Resource
{
    protected static ?string $model = Disposal::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static UnitEnum|string|null $navigationGroup = 'Events & Logs';

    protected static ?string $navigationLabel = 'Disposals';

    protected static ?string $modelLabel = 'Disposal';

    protected static ?string $pluralModelLabel = 'Disposals';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return DisposalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DisposalsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDisposals::route('/'),
            'create' => CreateDisposal::route('/create'),
            'edit' => EditDisposal::route('/{record}/edit'),
        ];
    }
}

