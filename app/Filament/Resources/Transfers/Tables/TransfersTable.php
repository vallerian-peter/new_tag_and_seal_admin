<?php

namespace App\Filament\Resources\Transfers\Tables;

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

class TransfersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('fromFarm.name')
                    ->label('From Farm')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('toFarm.name')
                    ->label('To Farm')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('livestock.identificationNumber')
                    ->label('Livestock Tag')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('transferDate')
                    ->label('Transfer Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Price')
                    ->money('TZS', true)
                    ->toggleable(),
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
                        ->modalHeading('Transfer Details')
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
                                            TextEntry::make('fromFarm.name')
                                                ->label('From Farm')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-arrow-left')
                                                ->color('warning'),
                                            TextEntry::make('toFarm.name')
                                                ->label('To Farm')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-arrow-right')
                                                ->color('success'),
                                            TextEntry::make('livestock.identificationNumber')
                                                ->label('Livestock Tag')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-tag')
                                                ->columnSpan(2),
                                        ]),
                                ]),
                            Section::make('Transfer Details')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('transferDate')
                                                ->label('Transfer Date')
                                                ->icon('heroicon-o-calendar')
                                                ->state(fn ($record) => blank($record->transferDate) ? '—' : Carbon::parse($record->transferDate)->format('d M Y')),
                                            TextEntry::make('status')
                                                ->label('Status')
                                                ->badge()
                                                ->weight(FontWeight::Bold)
                                                ->color('info')
                                                ->default('—'),
                                            TextEntry::make('transporterId')
                                                ->label('Transporter ID')
                                                ->icon('heroicon-o-truck')
                                                ->default('—'),
                                            TextEntry::make('price')
                                                ->label('Price')
                                                ->icon('heroicon-o-currency-dollar')
                                                ->default('—'),
                                        ]),
                                ]),
                            Section::make('Additional Information')
                                ->schema([
                                    TextEntry::make('reason')
                                        ->label('Reason')
                                        ->default('—')
                                        ->columnSpanFull(),
                                    TextEntry::make('remarks')
                                        ->label('Remarks')
                                        ->default('—')
                                        ->columnSpanFull(),
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

