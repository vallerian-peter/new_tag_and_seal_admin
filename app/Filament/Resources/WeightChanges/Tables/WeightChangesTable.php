<?php

namespace App\Filament\Resources\WeightChanges\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;

class WeightChangesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('eventDate', 'desc')
            ->columns([
                TextColumn::make('#')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('farm.name')
                    ->label('Farm')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                TextColumn::make('livestock.identificationNumber')
                    ->label('Livestock')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('eventDate')
                    ->label('Event Date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('previousWeight')
                    ->label('Previous (kg)')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('currentWeight')
                    ->label('Current (kg)')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('weightDifference')
                    ->label('Difference (kg)')
                    ->getStateUsing(fn ($record) => $record->currentWeight - $record->previousWeight)
                    ->numeric(decimalPlaces: 2)
                    ->badge()
                    ->color(fn ($state) => $state >= 0 ? 'success' : 'danger'),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ,
            ])
            ->filters([
                SelectFilter::make('farmUuid')
                    ->label('Farm')
                    ->relationship('farm', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('livestockUuid')
                    ->label('Livestock')
                    ->relationship('livestock', 'identificationNumber')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('view')
                        ->label('View')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Weight Change Details')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close')
                        ->modalWidth('2xl')
                        ->infolist([
                            Section::make('Basic Information')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
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
                            Section::make('Weight Information')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('oldWeight')
                                                ->label('Old Weight (kg)')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-arrow-down')
                                                ->color('warning')
                                                ->default('—'),
                                            TextEntry::make('newWeight')
                                                ->label('New Weight (kg)')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-arrow-up')
                                                ->color('success')
                                                ->default('—'),
                                        ]),
                                ]),
                            Section::make('Additional Information')
                                ->schema([
                                    TextEntry::make('remarks')
                                        ->label('Remarks')
                                        ->default('—')
                                        ->columnSpanFull(),
                                ]),
                            Section::make('Date Information')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('eventDate')
                                                ->label('Event Date')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-calendar')
                                                ->formatStateUsing(fn ($record, $state) => blank($state) && blank($record->created_at) 
                                                    ? '—' 
                                                    : Carbon::parse($state ?? $record->created_at)->format('d M Y, H:i')),
                                            TextEntry::make('created_at')
                                                ->label('Created At')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-clock')
                                                ->formatStateUsing(fn ($state) => blank($state) ? '—' : Carbon::parse($state)->format('d M Y, H:i')),
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


