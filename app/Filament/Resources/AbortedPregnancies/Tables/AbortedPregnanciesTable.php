<?php

namespace App\Filament\Resources\AbortedPregnancies\Tables;

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

class AbortedPregnanciesTable
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
                    ->sortable(),
                TextColumn::make('livestock.identificationNumber')
                    ->label('Livestock Tag')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('eventDate')
                    ->label('Event Date')
                    ->dateTime()
                    ->sortable()
                    ->formatStateUsing(fn ($record, $state) => $state ?? $record->created_at),
                TextColumn::make('abortionDate')
                    ->label('Abortion Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('reproductiveProblem.name')
                    ->label('Reproductive Problem')
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
                        ->modalHeading('Aborted Pregnancy Details')
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
                                        ]),
                                ]),
                            Section::make('Abortion Details')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('abortionDate')
                                                ->label('Abortion Date')
                                                ->icon('heroicon-o-calendar')
                                                ->state(fn ($record) => blank($record->abortionDate) ? '—' : Carbon::parse($record->abortionDate)->format('d M Y, h:i A')),
                                            TextEntry::make('status')
                                                ->label('Status')
                                                ->badge()
                                                ->weight(FontWeight::Bold)
                                                ->color(fn (string $state): string => match ($state) {
                                                    'active' => 'danger',
                                                    'resolved' => 'success',
                                                    'cancelled' => 'gray',
                                                    default => 'warning',
                                                })
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

