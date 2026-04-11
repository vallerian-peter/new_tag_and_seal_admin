<?php

namespace App\Filament\Resources\TailDockings;

use App\Filament\Resources\TailDockings\Pages\CreateTailDocking;
use App\Filament\Resources\TailDockings\Pages\EditTailDocking;
use App\Filament\Resources\TailDockings\Pages\ListTailDockings;
use App\Filament\Resources\TailDockings\Schemas\TailDockingForm;
use App\Filament\Resources\TailDockings\Tables\TailDockingsTable;
use App\Models\TailDocking;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class TailDockingResource extends Resource
{
    protected static ?string $model = TailDocking::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static UnitEnum|string|null $navigationGroup = 'Events & Logs';
    protected static ?string $navigationLabel = 'Tail Dockings';
    protected static ?string $modelLabel = 'Tail Docking';
    protected static ?string $pluralModelLabel = 'Tail Dockings';
    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return TailDockingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TailDockingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTailDockings::route('/'),
            'create' => CreateTailDocking::route('/create'),
            'edit' => EditTailDocking::route('/{record}/edit'),
        ];
    }
}

