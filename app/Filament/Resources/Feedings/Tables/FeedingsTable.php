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
            ->columns([
                TextColumn::make('#')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('feedingType.name')
                    ->label('Feeding Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('farm.name')
                    ->label('Farm')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('livestock.identificationNumber')
                    ->label('Livestock Tag')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nextFeedingTime')
                    ->label('Next Feeding Time')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Recorded At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
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


