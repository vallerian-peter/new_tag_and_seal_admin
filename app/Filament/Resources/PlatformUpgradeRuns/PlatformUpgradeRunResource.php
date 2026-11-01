<?php

namespace App\Filament\Resources\PlatformUpgradeRuns;

use App\Filament\Resources\PlatformUpgradeRuns\Pages\ListPlatformUpgradeRuns;
use App\Models\PlatformUpgradeRun;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class PlatformUpgradeRunResource extends Resource
{
    protected static ?string $model = PlatformUpgradeRun::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static UnitEnum|string|null $navigationGroup = 'System & Security';

    protected static ?string $navigationLabel = 'Upgrade Runs';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPlatformPermission('upgrades.view') ?? false;
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('started_at', 'desc')->columns([
            TextColumn::make('uuid')->label('Run')->copyable()->limit(16),
            TextColumn::make('release')->label('Release')->searchable(),
            TextColumn::make('environment')->badge(),
            TextColumn::make('state')->badge(),
            TextColumn::make('started_at')->dateTime(),
            TextColumn::make('completed_at')->dateTime()->placeholder('In progress'),
            TextColumn::make('backup_reference')->label('Backup reference')->wrap()->toggleable(isToggledHiddenByDefault: true),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListPlatformUpgradeRuns::route('/')];
    }
}
