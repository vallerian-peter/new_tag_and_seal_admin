<?php

namespace App\Filament\Resources\FinanceExpenses\Tables;

use App\Models\Farm;
use App\Models\Farmer;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FinanceExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('#')->label('#')->rowIndex(),
                TextColumn::make('sourceType')
                    ->label('Source Type')
                    ->badge()
                    ->searchable(),
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
                TextColumn::make('billNo')
                    ->label('Bill No')
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
                TextColumn::make('unitCost')
                    ->label('Unit')
                    ->money('TZS', locale: 'en')
                    ->sortable(),
                TextColumn::make('totalCost')
                    ->label('Total')
                    ->money('TZS', locale: 'en')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid', 'completed' => 'success',
                        default => 'warning',
                    }),
                TextColumn::make('expenseDate')
                    ->label('Expense Date')
                    ->dateTime()
                    ->sortable(),
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
                        'completed' => 'Completed',
                    ]),
                SelectFilter::make('sourceType')
                    ->options(fn () => \App\Models\FinanceExpense::query()
                        ->whereNotNull('sourceType')
                        ->distinct()
                        ->pluck('sourceType', 'sourceType')
                        ->toArray()
                    ),
                SelectFilter::make('farmUuid')
                    ->label('Farm')
                    ->options(fn () => Farm::query()->orderBy('name')->pluck('name', 'uuid')->toArray())
                    ->searchable(),
                SelectFilter::make('farmerId')
                    ->label('Farmer')
                    ->options(fn () => Farmer::query()
                        ->orderBy('firstName')
                        ->get(['id', 'firstName', 'surname'])
                        ->mapWithKeys(fn (Farmer $f) => [$f->id => trim(($f->firstName ?? '').' '.($f->surname ?? '')) ?: (string) $f->id])
                        ->toArray()
                    )
                    ->searchable(),
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading('Expenditure Details')
                    ->schema([
                        Section::make('Expenditure Information')
                            ->schema([
                                TextEntry::make('sourceType')
                                    ->label('Source Type')
                                    ->badge(),
                                TextEntry::make('billNo')
                                    ->label('Bill No.')
                                    ->placeholder('—'),
                                TextEntry::make('farm.name')
                                    ->label('Farm')
                                    ->placeholder('—'),
                                TextEntry::make('farmer_display')
                                    ->label('Farmer')
                                    ->state(fn ($record) => trim(($record->farmer?->firstName ?? $record->farm?->farmer?->firstName ?? '').' '.($record->farmer?->surname ?? $record->farm?->farmer?->surname ?? '')) ?: '—'),
                                TextEntry::make('subjectType')
                                    ->label('Subject Type')
                                    ->badge()
                                    ->placeholder('—'),
                                TextEntry::make('quantity')
                                    ->label('Quantity'),
                                TextEntry::make('unitCost')
                                    ->label('Unit Cost')
                                    ->money('TZS', locale: 'en'),
                                TextEntry::make('totalCost')
                                    ->label('Total Cost')
                                    ->money('TZS', locale: 'en')
                                    ->weight('bold'),
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'paid', 'completed' => 'success',
                                        default => 'warning',
                                    }),
                                TextEntry::make('expenseDate')
                                    ->label('Expense Date')
                                    ->dateTime()
                                    ->placeholder('—'),
                                TextEntry::make('notes')
                                    ->label('Notes')
                                    ->placeholder('—')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                        Section::make('Timestamps')
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime(),
                                TextEntry::make('updated_at')
                                    ->label('Updated At')
                                    ->dateTime(),
                            ])
                            ->columns(2),
                    ]),
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
