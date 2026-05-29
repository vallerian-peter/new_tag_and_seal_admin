<?php

namespace App\Filament\Resources\FinanceIncomes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FinanceIncomesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('#')->label('#')->rowIndex(),
                TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sourceType')
                    ->label('Source Type')
                    ->badge()
                    ->searchable(),
                TextColumn::make('farm.name')
                    ->label('Farm')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('farmer')
                    ->label('Farmer')
                    ->state(fn ($record) => trim(($record->farmer?->firstName ?? '').' '.($record->farmer?->surname ?? '')))
                    ->placeholder('—')
                    ->searchable(['farmer.firstName', 'farmer.surname']),
                TextColumn::make('referenceNo')
                    ->label('Reference No')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('subjectType')
                    ->label('Subject')
                    ->badge()
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label('Qty')
                    ->sortable(),
                TextColumn::make('unitAmount')
                    ->label('Unit')
                    ->money('TZS', locale: 'en')
                    ->sortable(),
                TextColumn::make('totalAmount')
                    ->label('Total')
                    ->money('TZS', locale: 'en')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'received' ? 'success' : 'warning'),
                TextColumn::make('incomeDate')
                    ->label('Income Date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'received' => 'Received',
                    ]),
                SelectFilter::make('sourceType')
                    ->label('Source Type')
                    ->options(fn () => \App\Models\FinanceIncome::query()
                        ->whereNotNull('sourceType')
                        ->distinct()
                        ->orderBy('sourceType')
                        ->pluck('sourceType', 'sourceType')
                        ->toArray()),
                Filter::make('incomeDate')
                    ->label('Date Range')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')->label('From'),
                        \Filament\Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('incomeDate', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('incomeDate', '<=', $date));
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
