<?php

namespace App\Filament\Resources\Dryoffs;

use App\Filament\Resources\Dryoffs\Pages\CreateDryoff;
use App\Filament\Resources\Dryoffs\Pages\EditDryoff;
use App\Filament\Resources\Dryoffs\Pages\ListDryoffs;
use App\Filament\Resources\Dryoffs\Schemas\DryoffForm;
use App\Filament\Resources\Dryoffs\Tables\DryoffsTable;
use App\Models\Dryoff;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class DryoffResource extends Resource
{
    protected static ?string $model = Dryoff::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static UnitEnum|string|null $navigationGroup = 'Events & Logs';

    protected static ?string $navigationLabel = 'Dryoff';

    protected static ?string $modelLabel = 'Dryoff';

    protected static ?string $pluralModelLabel = 'Dryoff Logs';

    protected static ?int $navigationSort = 9;

    public static function form(Schema $schema): Schema
    {
        return DryoffForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DryoffsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDryoffs::route('/'),
            'create' => CreateDryoff::route('/create'),
            'edit' => EditDryoff::route('/{record}/edit'),
        ];
    }
}

