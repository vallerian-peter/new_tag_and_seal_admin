<?php

namespace App\Filament\Resources\Medications\Tables;

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

class MedicationsTable
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
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('farm.name')
                    ->label('Farm')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('livestock.identificationNumber')
                    ->label('Livestock Tag')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('disease.name')
                    ->label('Disease')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('medicine.name')
                    ->label('Medicine')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('medicationDate')
                    ->label('Medication Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label('Quantity')
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
                        ->modalHeading('Medication Details')
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
                            Section::make('Medication Details')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('disease.name')
                                                ->label('Disease')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-exclamation-triangle')
                                                ->color('warning')
                                                ->default('—'),
                                            TextEntry::make('medicine.name')
                                                ->label('Medicine')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-beaker')
                                                ->color('success'),
                                            TextEntry::make('medicationDate')
                                                ->label('Medication Date')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-calendar')
                                                ->state(fn ($record) => blank($record->medicationDate) ? '—' : Carbon::parse($record->medicationDate)->format('d M Y')),
                                            TextEntry::make('quantity')
                                                ->label('Quantity')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-scale')
                                                ->default('—'),
                                            TextEntry::make('withdrawalPeriod')
                                                ->label('Withdrawal Period')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-clock')
                                                ->color('info')
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

