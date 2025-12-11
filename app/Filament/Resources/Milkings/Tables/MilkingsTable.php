<?php

namespace App\Filament\Resources\Milkings\Tables;

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

class MilkingsTable
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
                TextColumn::make('session')
                    ->label('Session')
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->toggleable(),
                TextColumn::make('milkingMethod.name')
                    ->label('Method')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Recorded')
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
                        ->modalHeading('Milking Details')
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
                            Section::make('Milking Details')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('milkingMethod.name')
                                                ->label('Method')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-cog')
                                                ->color('info')
                                                ->default('—'),
                                            TextEntry::make('session')
                                                ->label('Session')
                                                ->icon('heroicon-o-clock')
                                                ->default('—'),
                                            TextEntry::make('amount')
                                                ->label('Amount')
                                                ->icon('heroicon-o-scale')
                                                ->default('—'),
                                            TextEntry::make('status')
                                                ->label('Status')
                                                ->badge()
                                                ->weight(FontWeight::Bold)
                                                ->color('success')
                                                ->default('—'),
                                        ]),
                                ]),
                            Section::make('Quality Measurements')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('lactometerReading')
                                                ->label('Lactometer Reading')
                                                ->icon('heroicon-o-chart-bar')
                                                ->default('—'),
                                            TextEntry::make('correctedLactometerReading')
                                                ->label('Corrected Lactometer Reading')
                                                ->icon('heroicon-o-chart-bar-square')
                                                ->default('—'),
                                            TextEntry::make('solid')
                                                ->label('Solid %')
                                                ->icon('heroicon-o-calculator')
                                                ->default('—'),
                                            TextEntry::make('solidNonFat')
                                                ->label('Solid Non Fat %')
                                                ->icon('heroicon-o-calculator')
                                                ->default('—'),
                                            TextEntry::make('protein')
                                                ->label('Protein %')
                                                ->icon('heroicon-o-beaker')
                                                ->default('—'),
                                            TextEntry::make('totalSolids')
                                                ->label('Total Solids %')
                                                ->icon('heroicon-o-calculator')
                                                ->default('—'),
                                            TextEntry::make('colonyFormingUnits')
                                                ->label('CFU')
                                                ->icon('heroicon-o-bug-ant')
                                                ->default('—'),
                                            TextEntry::make('acidity')
                                                ->label('Acidity')
                                                ->icon('heroicon-o-beaker')
                                                ->default('—'),
                                        ]),
                                ]),
                            Section::make('Timestamps')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('created_at')
                                                ->label('Recorded At')
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

