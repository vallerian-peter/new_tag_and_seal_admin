<?php

namespace App\Filament\Resources\EventDefinitions\Tables;

use App\Filament\Resources\AnimalBlueprints\AnimalBlueprintResource;
use App\Models\AnimalBlueprint;
use App\Models\EventDefinition;
use App\Services\BehaviorMapping;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EventDefinitionsTable
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
                TextColumn::make('name')
                    ->label('Event Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('code')
                    ->label('Code')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('blueprint.code')
                    ->label('Blueprint')
                    ->searchable()
                    ->sortable()
                    ->url(fn (EventDefinition $record) => $record->blueprint ? AnimalBlueprintResource::getUrl('preview', ['record' => $record->blueprint->uuid]) : null),
                TextColumn::make('behavior.name')
                    ->label('Behaviour')
                    ->default(fn (EventDefinition $record) => $record->behavior_code ? str($record->behavior_code)->headline() : 'None'),
                TextColumn::make('family')
                    ->label('Family')
                    ->badge()
                    ->color('info')
                    ->default(fn (EventDefinition $record) => BehaviorMapping::FAMILIES[$record->family ?? $record->behavior?->family] ?? 'Other'),
                TextColumn::make('form.code')
                    ->label('Form')
                    ->searchable()
                    ->toggleable(),
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
                SelectFilter::make('blueprint_uuid')
                    ->label('Blueprint')
                    ->options(fn () => AnimalBlueprint::orderBy('code')->pluck('code', 'uuid')),
                SelectFilter::make('family')
                    ->label('Family')
                    ->options(BehaviorMapping::FAMILIES),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(['draft' => 'Draft', 'published' => 'Published', 'retired' => 'Retired']),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('preview_form')
                        ->label('Preview Flow')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->url(fn (EventDefinition $record) => $record->blueprint ? AnimalBlueprintResource::getUrl('preview', ['record' => $record->blueprint->uuid]) : null),
                ]),
            ]);
    }
}
