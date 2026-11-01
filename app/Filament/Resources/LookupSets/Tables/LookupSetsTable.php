<?php

namespace App\Filament\Resources\LookupSets\Tables;

use App\Models\LookupSet;
use App\Services\DynamicConfigurationService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LookupSetsTable
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
                    ->label('Lookup Set Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('code')
                    ->label('Code')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('version')
                    ->label('Version')
                    ->sortable()
                    ->badge(),
                TextColumn::make('parent.name')
                    ->label('Parent List')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('options_count')
                    ->label('Choices')
                    ->counts('options')
                    ->badge()
                    ->color('info'),
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
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(['draft' => 'Draft', 'published' => 'Published', 'retired' => 'Retired']),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('clone')
                        ->label('Clone Lookup Set')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('warning')
                        ->visible(fn (LookupSet $record) => $record->status !== 'draft' && (auth()->user()?->hasPlatformPermission('configuration.manage') ?? false))
                        ->action(function (LookupSet $record): void {
                            app(DynamicConfigurationService::class)->cloneLookup($record, auth()->user());
                        }),
                ]),
            ]);
    }
}
