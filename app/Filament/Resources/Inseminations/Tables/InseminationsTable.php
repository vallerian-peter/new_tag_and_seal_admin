<?php

namespace App\Filament\Resources\Inseminations\Tables;

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

class InseminationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
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
                TextColumn::make('inseminationDate')
                    ->label('Insemination Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('bullCode')
                    ->label('Bull Code')
                    ->toggleable(),
                TextColumn::make('semenBatchNumber')
                    ->label('Batch No.')
                    ->toggleable(),
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
                        ->modalHeading('Insemination Details')
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
                                            TextEntry::make('livestock.identificationNumber')
                                                ->label('Livestock Tag')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-tag'),
                                        ]),
                                ]),
                            Section::make('Insemination Details')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('currentHeatType.name')
                                                ->label('Current Heat Type')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-fire')
                                                ->color('warning')
                                                ->default('—'),
                                            TextEntry::make('inseminationService.name')
                                                ->label('Service')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-wrench-screwdriver')
                                                ->color('info')
                                                ->default('—'),
                                            TextEntry::make('semenStrawType.name')
                                                ->label('Semen Straw Type')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-cube')
                                                ->default('—'),
                                            TextEntry::make('lastHeatDate')
                                                ->label('Last Heat Date')
                                                ->icon('heroicon-o-calendar')
                                                ->state(fn ($record) => blank($record->lastHeatDate) ? '—' : Carbon::parse($record->lastHeatDate)->format('d M Y')),
                                            TextEntry::make('inseminationDate')
                                                ->label('Insemination Date')
                                                ->icon('heroicon-o-calendar-days')
                                                ->state(fn ($record) => blank($record->inseminationDate) ? '—' : Carbon::parse($record->inseminationDate)->format('d M Y')),
                                        ]),
                                ]),
                            Section::make('Bull Information')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('bullCode')
                                                ->label('Bull Code')
                                                ->icon('heroicon-o-hashtag')
                                                ->default('—'),
                                            TextEntry::make('bullBreed')
                                                ->label('Bull Breed')
                                                ->icon('heroicon-o-tag')
                                                ->default('—'),
                                        ]),
                                ]),
                            Section::make('Semen Details')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('semenProductionDate')
                                                ->label('Semen Production Date')
                                                ->icon('heroicon-o-calendar')
                                                ->state(fn ($record) => blank($record->semenProductionDate) ? '—' : Carbon::parse($record->semenProductionDate)->format('d M Y')),
                                            TextEntry::make('productionCountry')
                                                ->label('Production Country')
                                                ->icon('heroicon-o-globe-alt')
                                                ->default('—'),
                                            TextEntry::make('semenBatchNumber')
                                                ->label('Semen Batch Number')
                                                ->icon('heroicon-o-hashtag')
                                                ->default('—'),
                                            TextEntry::make('internationalId')
                                                ->label('International ID')
                                                ->icon('heroicon-o-identification')
                                                ->default('—'),
                                            TextEntry::make('aiCode')
                                                ->label('AI Code')
                                                ->icon('heroicon-o-code-bracket')
                                                ->default('—'),
                                            TextEntry::make('manufacturerName')
                                                ->label('Manufacturer')
                                                ->icon('heroicon-o-building-office')
                                                ->default('—'),
                                            TextEntry::make('semenSupplier')
                                                ->label('Supplier')
                                                ->icon('heroicon-o-truck')
                                                ->default('—')
                                                ->columnSpan(2),
                                        ]),
                                ]),
                            Section::make('Timestamps')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('created_at')
                                                ->label('Created At')
                                                ->dateTime()
                                                ->icon('heroicon-o-calendar'),
                                            TextEntry::make('updated_at')
                                                ->label('Updated At')
                                                ->dateTime()
                                                ->icon('heroicon-o-calendar-days'),
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

