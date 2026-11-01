<?php

namespace App\Filament\Resources\AnimalBlueprints\Tables;

use App\Filament\Resources\AnimalBlueprints\AnimalBlueprintResource;
use App\Models\AnimalBlueprint;
use App\Models\AnimalSpecies;
use App\Services\DynamicConfigurationService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class AnimalBlueprintsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->selectable()
            ->columns([
                TextColumn::make('#')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('species.name')
                    ->label('Species')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('code')
                    ->label('Blueprint Code')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('mobile_type')
                    ->label('Animal Type')
                    ->badge()
                    ->color('info')
                    ->state(function (AnimalBlueprint $record) {
                        $typeId = $record->settings_json['catalogBinding']['livestockTypeId'] ?? $record->species?->legacy_livestock_type_id;
                        return $typeId ? DB::table('livestock_types')->where('id', $typeId)->value('name') : 'Configured';
                    })
                    ->sortable(),
                TextColumn::make('version')
                    ->label('Version')
                    ->sortable()
                    ->badge(),
                TextColumn::make('events_count')
                    ->label('Events')
                    ->badge()
                    ->color('success')
                    ->state(fn (AnimalBlueprint $record) => DB::table('event_definitions')->where('blueprint_uuid', $record->uuid)->count()),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'retired' => 'gray',
                        default => 'info',
                    }),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('species_uuid')
                    ->label('Species')
                    ->options(fn () => AnimalSpecies::orderBy('name')->pluck('name', 'uuid')),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(['draft' => 'Draft', 'published' => 'Published', 'retired' => 'Retired']),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('preview')
                        ->label('Preview Flow')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->url(fn (AnimalBlueprint $record) => AnimalBlueprintResource::getUrl('preview', ['record' => $record])),
                    Action::make('clone')
                        ->label('Clone Blueprint')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('warning')
                        ->visible(fn (AnimalBlueprint $record) => $record->status !== 'draft' && (auth()->user()?->hasPlatformPermission('configuration.manage') ?? false))
                        ->action(function (AnimalBlueprint $record): void {
                            app(DynamicConfigurationService::class)->cloneBlueprint($record, auth()->user());
                        }),
                ]),
            ]);
    }
}
