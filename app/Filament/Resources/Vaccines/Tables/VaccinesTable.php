<?php

namespace App\Filament\Resources\Vaccines\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
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
                TextColumn::make('uuid')
                    ->label('UUID')
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Vaccine Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('lot')
                    ->label('Lot')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('formulationType')
                    ->label('Formulation Type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('vaccineType.name')
                    ->label('Vaccine Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                ActionGroup::make([
                    Action::make('view')
                        ->label('View')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Vaccine Details')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close')
                        ->modalWidth('lg')
                        ->infolist([
                            TextEntry::make('uuid')
                                ->label('UUID')
                                ->weight(FontWeight::Bold),
                            TextEntry::make('name')
                                ->label('Vaccine Name')
                                ->weight(FontWeight::Bold),
                            TextEntry::make('lot')
                                ->label('Lot')
                                ->weight(FontWeight::Bold),
                            TextEntry::make('formulationType')
                                ->label('Formulation Type')
                                ->weight(FontWeight::Bold),
                            TextEntry::make('dose')
                                ->label('Dose')
                                ->weight(FontWeight::Bold)
                                ->default('—'),
                            TextEntry::make('status')
                                ->label('Status')
                                ->weight(FontWeight::Bold),
                            TextEntry::make('vaccineType.name')
                                ->label('Vaccine Type')
                                ->weight(FontWeight::Bold)
                                ->default('—'),
                            TextEntry::make('vaccineSchedule')
                                ->label('Vaccine Schedule')
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

