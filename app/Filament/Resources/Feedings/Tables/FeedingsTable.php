<?php

namespace App\Filament\Resources\Feedings\Tables;

use Carbon\Carbon;
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

class FeedingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('eventDate', 'desc')
            ->columns([
                TextColumn::make('#')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('feedingType.name')
                    ->label('Feeding Type')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                TextColumn::make('farm.name')
                    ->label('Farm')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('livestock.identificationNumber')
                    ->label('Livestock')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('eventDate')
                    ->label('Event Date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('feedGiven')
                    ->label('Feed Given')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('quantityUnit')
                    ->label('Unit')
                    ->badge(),
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
                SelectFilter::make('feedingTypeId')
                    ->label('Feeding Type')
                    ->relationship('feedingType', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('view')
                        ->label('View')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Feeding Details')
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
                            Section::make('Feeding Details')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('feedingType.name')
                                                ->label('Feeding Type')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-bolt')
                                                ->color('success'),
                                            TextEntry::make('nextFeedingTime')
                                                ->label('Next Feeding Time')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-calendar')
                                                ->formatStateUsing(fn ($state) => blank($state) ? '—' : Carbon::parse($state)->format('d M Y, H:i')),
                                            TextEntry::make('amount')
                                                ->label('Amount')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-scale')
                                                ->columnSpan(2),
                                        ]),
                                ]),
                            Section::make('Additional Information')
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
                                                ->label('Recorded At')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-clock')
                                                ->formatStateUsing(fn ($state) => blank($state) ? '—' : Carbon::parse($state)->format('d M Y, H:i')),
                                        ]),
                                    TextEntry::make('remarks')
                                        ->label('Remarks')
                                        ->default('—')
                                        ->columnSpanFull(),
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


