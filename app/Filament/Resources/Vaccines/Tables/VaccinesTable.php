<?php

namespace App\Filament\Resources\Vaccines\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;

class VaccinesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('#')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('name')
                    ->label('Vaccine Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('farm.name')
                    ->label('Farm')
                    ->searchable()
                    ->sortable()
                    ->default('—'),
                TextColumn::make('lot')
                    ->label('Lot')
                    ->searchable()
                    ->sortable()
                    ->default('—'),
                TextColumn::make('formulationType')
                    ->label('Formulation')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'live-attenuated' => 'Live Attenuated',
                        'inactivated' => 'Inactivated',
                        default => $state ?? '—',
                    }),
                TextColumn::make('dose')
                    ->label('Dose')
                    ->sortable()
                    ->default('—'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'expired' => 'danger',
                        'inactive', 'recalled' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('vaccineType.name')
                    ->label('Type')
                    ->sortable()
                    ->default('—'),
                TextColumn::make('vaccineSchedule')
                    ->label('Schedule')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'regular' => 'Regular Schedule',
                        'booster' => 'Booster',
                        'seasonal' => 'Seasonal',
                        'emergency' => 'Emergency',
                        default => $state ?? '—',
                    })
                    ->sortable()
                    ->default('—'),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->modalHeading('Vaccine Details')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close')
                        ->modalWidth('lg')
                        ->infolist([
                            TextEntry::make('name')
                                ->label('Vaccine Name')
                                ->weight(FontWeight::Bold),
                            TextEntry::make('farm.name')
                                ->label('Farm')
                                ->weight(FontWeight::Bold)
                                ->default('—'),
                            TextEntry::make('lot')
                                ->label('Lot')
                                ->weight(FontWeight::Bold)
                                ->default('—'),
                            TextEntry::make('formulationType')
                                ->label('Formulation Type')
                                ->badge()
                                ->formatStateUsing(fn (?string $state): string => match ($state) {
                                    'live-attenuated' => 'Live Attenuated',
                                    'inactivated' => 'Inactivated',
                                    default => $state ?? '—',
                                })
                                ->weight(FontWeight::Bold),
                            TextEntry::make('dose')
                                ->label('Dose')
                                ->weight(FontWeight::Bold)
                                ->default('—'),
                            TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'active' => 'success',
                                    'expired' => 'danger',
                                    'inactive', 'recalled' => 'warning',
                                    default => 'gray',
                                })
                                ->weight(FontWeight::Bold),
                            TextEntry::make('vaccineType.name')
                                ->label('Vaccine Type')
                                ->weight(FontWeight::Bold)
                                ->default('—'),
                            TextEntry::make('vaccineSchedule')
                                ->label('Vaccine Schedule')
                                ->formatStateUsing(fn (?string $state): string => match ($state) {
                                    'regular' => 'Regular Schedule',
                                    'booster' => 'Booster',
                                    'seasonal' => 'Seasonal',
                                    'emergency' => 'Emergency',
                                    default => $state ?? '—',
                                })
                                ->weight(FontWeight::Bold)
                                ->default('—'),
                            TextEntry::make('created_at')
                                ->label('Created At')
                                ->dateTime(),
                            TextEntry::make('updated_at')
                                ->label('Updated At')
                                ->dateTime(),
                        ]),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

