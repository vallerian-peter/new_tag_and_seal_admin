<?php

namespace App\Filament\Resources\BirthEvents\Tables;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BirthEventsTable
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
                TextColumn::make('eventType')
                    ->label('Event Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'calving' => 'info',
                        'farrowing' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'calving' => 'Calving',
                        'farrowing' => 'Farrowing',
                        default => $state,
                    })
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
                TextColumn::make('birthType.name')
                    ->label('Birth Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
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
                        ->modalHeading(fn ($record) => ($record->eventType === 'farrowing' ? 'Farrowing' : 'Calving') . ' Event Details')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close')
                        ->modalWidth('2xl')
                        ->infolist([
                            Section::make('Basic Information')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('uuid')
                                                ->label('UUID')
                                                ->weight(FontWeight::Bold)
                                                ->copyable()
                                                ->columnSpan(2),
                                            TextEntry::make('farm.name')
                                                ->label('Farm')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-home'),
                                            TextEntry::make('farm.referenceNo')
                                                ->label('Farm Reference')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-hashtag'),
                                            TextEntry::make('livestock.identificationNumber')
                                                ->label('Livestock Tag')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-tag'),
                                            TextEntry::make('livestock.name')
                                                ->label('Livestock Name')
                                                ->weight(FontWeight::Bold)
                                                ->default('—'),
                                            TextEntry::make('eventType')
                                                ->label('Event Type')
                                                ->badge()
                                                ->color(fn (string $state): string => match ($state) {
                                                    'calving' => 'info',
                                                    'farrowing' => 'warning',
                                                    default => 'gray',
                                                })
                                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                                    'calving' => 'Calving (Cattle)',
                                                    'farrowing' => 'Farrowing (Pig)',
                                                    default => $state,
                                                }),
                                            TextEntry::make('status')
                                                ->label('Status')
                                                ->badge()
                                                ->weight(FontWeight::Bold)
                                                ->color(fn (string $state): string => match ($state) {
                                                    'active' => 'success',
                                                    'completed' => 'info',
                                                    'cancelled' => 'danger',
                                                    default => 'gray',
                                                })
                                                ->default('—'),
                                        ]),
                                ]),
                            Section::make('Event Timeline')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('startDate')
                                                ->label('Start Date')
                                                ->icon('heroicon-o-calendar')
                                                ->state(fn ($record) => blank($record->startDate) ? '—' : Carbon::parse($record->startDate)->format('d M Y, h:i A')),
                                            TextEntry::make('endDate')
                                                ->label('End Date')
                                                ->icon('heroicon-o-calendar-days')
                                                ->state(fn ($record) => blank($record->endDate) ? '—' : Carbon::parse($record->endDate)->format('d M Y, h:i A')),
                                        ]),
                                ]),
                            Section::make('Medical Details')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('birthType.name')
                                                ->label(fn ($record) => $record->eventType === 'farrowing' ? 'Farrowing Type' : 'Birth Type')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-clipboard-document-check')
                                                ->color('success')
                                                ->default('—'),
                                            TextEntry::make('birthProblem.name')
                                                ->label(fn ($record) => $record->eventType === 'farrowing' ? 'Farrowing Problem' : 'Birth Problem')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-exclamation-triangle')
                                                ->color('warning')
                                                ->default('—'),
                                            TextEntry::make('reproductiveProblem.name')
                                                ->label('Reproductive Problem')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-exclamation-circle')
                                                ->color('danger')
                                                ->default('—')
                                                ->columnSpan(2),
                                        ]),
                                ]),
                            Section::make('Additional Information')
                                ->schema([
                                    TextEntry::make('remarks')
                                        ->label('Remarks')
                                        ->default('—')
                                        ->columnSpanFull(),
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('created_at')
                                                ->label('Created At')
                                                ->icon('heroicon-o-clock')
                                                ->dateTime(),
                                            TextEntry::make('updated_at')
                                                ->label('Updated At')
                                                ->icon('heroicon-o-arrow-path')
                                                ->dateTime(),
                                        ]),
                                ]),
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

