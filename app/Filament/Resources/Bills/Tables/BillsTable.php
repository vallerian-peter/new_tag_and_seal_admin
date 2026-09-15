<?php

namespace App\Filament\Resources\Bills\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BillsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('#')->label('#')->rowIndex(),
                TextColumn::make('billNo')
                    ->label('Bill No')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('farm.name')
                    ->label('Farm')
                    ->searchable()
                    ->sortable()
                    ->default('—'),
                TextColumn::make('farmer.full_name')
                    ->label('Farmer')
                    ->state(fn ($record) => trim(($record->farmer?->firstName ?? $record->farm?->farmer?->firstName ?? '').' '.($record->farmer?->surname ?? $record->farm?->farmer?->surname ?? '')))
                    ->placeholder('—')
                    ->searchable(query: function ($query, string $search) {
                        return $query->where(function ($sub) use ($search) {
                            $sub->whereHas('farmer', function ($q) use ($search) {
                                $q->whereRaw("CONCAT(COALESCE(firstName, ''), ' ', COALESCE(middleName, ''), ' ', COALESCE(surname, '')) LIKE ?", ["%{$search}%"]);
                            })->orWhereHas('farm.farmer', function ($q) use ($search) {
                                $q->whereRaw("CONCAT(COALESCE(firstName, ''), ' ', COALESCE(middleName, ''), ' ', COALESCE(surname, '')) LIKE ?", ["%{$search}%"]);
                            });
                        });
                    }),
                TextColumn::make('extensionOfficer.full_name')
                    ->label('Extension Officer')
                    ->state(fn ($record) => trim(($record->extensionOfficer?->firstName ?? '').' '.($record->extensionOfficer?->lastName ?? '')))
                    ->placeholder('—')
                    ->searchable(query: function ($query, string $search) {
                        return $query->whereHas('extensionOfficer', function ($q) use ($search) {
                            $q->whereRaw("CONCAT(COALESCE(firstName, ''), ' ', COALESCE(middleName, ''), ' ', COALESCE(lastName, '')) LIKE ?", ["%{$search}%"]);
                        });
                    }),
                TextColumn::make('subjectType')
                    ->label('Subject')
                    ->badge()
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label('Qty')
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('TZS', locale: 'en')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'paid' ? 'success' : 'warning'),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                    ]),
                SelectFilter::make('farmUuid')
                    ->label('Farm')
                    ->relationship('farm', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('farmerId')
                    ->label('Farmer')
                    ->relationship('farmer', 'firstName')
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->firstName ?? '').' '.($record->surname ?? '')))
                    ->searchable()
                    ->preload(),
                SelectFilter::make('subjectType')
                    ->label('Subject Type')
                    ->options(fn () => \App\Models\Bill::query()
                        ->whereNotNull('subjectType')
                        ->distinct()
                        ->pluck('subjectType', 'subjectType')
                        ->toArray()),
                Filter::make('created_at')
                    ->label('Date Range')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')->label('From'),
                        \Filament\Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->modalHeading('Bill Details')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close')
                        ->modalWidth('lg')
                        ->infolist([
                            TextEntry::make('billNo')
                                ->label('Bill No')
                                ->weight(FontWeight::Bold),
                            TextEntry::make('farm.name')
                                ->label('Farm')
                                ->weight(FontWeight::Bold)
                                ->default('—'),
                            TextEntry::make('farmer')
                                ->label('Farmer')
                                ->state(fn ($record) => trim(($record->farmer?->firstName ?? '').' '.($record->farmer?->surname ?? '')))
                                ->weight(FontWeight::Bold)
                                ->default('—'),
                            TextEntry::make('extensionOfficer')
                                ->label('Extension Officer')
                                ->state(fn ($record) => trim(($record->extensionOfficer?->firstName ?? '').' '.($record->extensionOfficer?->lastName ?? '')))
                                ->weight(FontWeight::Bold)
                                ->default('—'),
                            TextEntry::make('subjectType')
                                ->label('Subject Type')
                                ->badge()
                                ->weight(FontWeight::Bold),
                            TextEntry::make('quantity')
                                ->label('Quantity')
                                ->weight(FontWeight::Bold),
                            TextEntry::make('amount')
                                ->label('Amount')
                                ->money('TZS', locale: 'en')
                                ->weight(FontWeight::Bold),
                            TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->color(fn (string $state): string => $state === 'paid' ? 'success' : 'warning')
                                ->weight(FontWeight::Bold),
                            TextEntry::make('notes')
                                ->label('Notes')
                                ->default('—')
                                ->columnSpanFull(),
                            TextEntry::make('created_at')
                                ->label('Created At')
                                ->dateTime(),
                            TextEntry::make('updated_at')
                                ->label('Updated At')
                                ->dateTime(),
                        ]),
                    Action::make('mark_paid')
                        ->label('Mark as Paid')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn ($record) => $record->status !== 'paid')
                        ->requiresConfirmation()
                        ->action(fn ($record) => $record->update(['status' => 'paid'])),
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
