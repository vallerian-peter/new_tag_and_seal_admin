<?php

namespace App\Filament\Resources\Pregnancies\Tables;

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

class PregnanciesTable
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
                TextColumn::make('testResult.name')
                    ->label('Test Result')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('noOfMonths')
                    ->label('Months')
                    ->sortable(),
                TextColumn::make('testDate')
                    ->label('Test Date')
                    ->date()
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
                        ->modalHeading('Pregnancy Details')
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
                            Section::make('Pregnancy Details')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('testResult.name')
                                                ->label('Test Result')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-clipboard-document-check')
                                                ->color('success')
                                                ->default('—'),
                                            TextEntry::make('noOfMonths')
                                                ->label('Number of Months')
                                                ->icon('heroicon-o-calendar')
                                                ->default('—'),
                                            TextEntry::make('testDate')
                                                ->label('Test Date')
                                                ->icon('heroicon-o-calendar-days')
                                                ->state(fn ($record) => blank($record->testDate) ? '—' : Carbon::parse($record->testDate)->format('d M Y')),
                                            TextEntry::make('status')
                                                ->label('Status')
                                                ->badge()
                                                ->weight(FontWeight::Bold)
                                                ->color('info')
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

