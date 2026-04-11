<?php

namespace App\Filament\Resources\TeethClippings\Tables;

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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TeethClippingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('eventDate', 'desc')
            ->columns([
                TextColumn::make('#')->label('#')->rowIndex(),
                TextColumn::make('uuid')->label('UUID')->searchable()->toggleable(),
                TextColumn::make('farm.name')->label('Farm')->searchable()->sortable(),
                TextColumn::make('livestock.identificationNumber')->label('Livestock Tag')->searchable()->sortable(),
                TextColumn::make('eventDate')->label('Event Date')->dateTime()->sortable()->formatStateUsing(fn ($record, $state) => $state ?? $record->created_at),
                TextColumn::make('method')->label('Method')->searchable()->badge()->toggleable(),
                TextColumn::make('created_at')->label('Created At')->dateTime()->sortable()->toggleable(),
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
                SelectFilter::make('method')
                    ->label('Method')
                    ->options(fn ($query) => $query->clone()->select('method')->whereNotNull('method')->distinct()->pluck('method', 'method')->toArray()),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('view')
                        ->label('View')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Teeth Clipping Details')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close')
                        ->modalWidth('2xl')
                        ->infolist([
                            Section::make('Basic Information')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('uuid')->label('UUID')->weight(FontWeight::Bold)->copyable()->columnSpan(2),
                                        TextEntry::make('farm.name')->label('Farm')->weight(FontWeight::Bold)->icon('heroicon-o-home'),
                                        TextEntry::make('livestock.identificationNumber')->label('Livestock Tag')->weight(FontWeight::Bold)->icon('heroicon-o-tag'),
                                    ]),
                                ]),
                            Section::make('Procedure Details')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('method')->label('Method')->badge()->default('—'),
                                        TextEntry::make('notes')->label('Notes')->default('—')->columnSpan(2),
                                    ]),
                                ]),
                            Section::make('Date Information')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('eventDate')
                                            ->label('Event Date')
                                            ->weight(FontWeight::Bold)
                                            ->icon('heroicon-o-calendar')
                                            ->formatStateUsing(fn ($record, $state) => blank($state) && blank($record->created_at) ? '—' : Carbon::parse($state ?? $record->created_at)->format('d M Y, H:i')),
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

