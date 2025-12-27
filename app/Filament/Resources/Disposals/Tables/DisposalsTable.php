<?php

namespace App\Filament\Resources\Disposals\Tables;

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
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DisposalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('eventDate', 'desc')
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
                TextColumn::make('eventDate')
                    ->label('Event Date')
                    ->dateTime()
                    ->sortable()
                    ->formatStateUsing(fn ($record, $state) => $state ?? $record->created_at),
                TextColumn::make('disposalType.name')
                    ->label('Disposal Type')
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'info' => 'approved',
                        'danger' => 'rejected',
                    ])
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
                        ->modalHeading('Disposal Details')
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
                            Section::make('Disposal Details')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('disposalType.name')
                                                ->label('Disposal Type')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-trash')
                                                ->color('danger'),
                                            TextEntry::make('status')
                                                ->label('Status')
                                                ->badge()
                                                ->weight(FontWeight::Bold)
                                                ->color(fn (string $state): string => match ($state) {
                                                    'pending' => 'warning',
                                                    'completed' => 'success',
                                                    'approved' => 'info',
                                                    'rejected' => 'danger',
                                                    default => 'gray',
                                                }),
                                        ]),
                                ]),
                            Section::make('Additional Information')
                                ->schema([
                                    TextEntry::make('reasons')
                                        ->label('Reasons')
                                        ->default('—')
                                        ->columnSpanFull(),
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

