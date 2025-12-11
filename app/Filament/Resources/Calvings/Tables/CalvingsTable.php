<?php

namespace App\Filament\Resources\Calvings\Tables;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CalvingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('farm.name')
                    ->label('Farm')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('livestock.identificationNumber')
                    ->label('Livestock Tag')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('startDate')
                    ->label('Start Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('endDate')
                    ->label('End Date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('calvingType.name')
                    ->label('Calving Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([])
            ->recordActions([
                ActionGroup::make([
                    Action::make('view')
                        ->label('View')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Calving Details')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close')
                        ->modalWidth('lg')
                        ->infolist([
                            TextEntry::make('uuid')
                                ->label('UUID')
                                ->weight(FontWeight::Bold),
                            TextEntry::make('farm.name')
                                ->label('Farm')
                                ->weight(FontWeight::Bold),
                            TextEntry::make('livestock.identificationNumber')
                                ->label('Livestock Tag')
                                ->weight(FontWeight::Bold),
                            TextEntry::make('startDate')
                                ->label('Start Date')
                                ->state(fn ($record) => blank($record->startDate) ? '—' : Carbon::parse($record->startDate)->format('d M Y')),
                            TextEntry::make('endDate')
                                ->label('End Date')
                                ->state(fn ($record) => blank($record->endDate) ? '—' : Carbon::parse($record->endDate)->format('d M Y')),
                            TextEntry::make('calvingType.name')
                                ->label('Calving Type')
                                ->weight(FontWeight::Bold)
                                ->default('—'),
                            TextEntry::make('calvingProblem.name')
                                ->label('Calving Problem')
                                ->weight(FontWeight::Bold)
                                ->default('—'),
                            TextEntry::make('reproductiveProblem.name')
                                ->label('Reproductive Problem')
                                ->weight(FontWeight::Bold)
                                ->default('—'),
                            TextEntry::make('remarks')
                                ->label('Remarks')
                                ->default('—'),
                            TextEntry::make('status')
                                ->label('Status')
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

