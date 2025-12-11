<?php

namespace App\Filament\Resources\Farms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FarmsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('name')
                    ->label('Farm Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('referenceNo')
                    ->label('Reference No.')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('farmer.firstName')
                    ->label('Farmer')
                    ->formatStateUsing(fn ($record) =>
                        $record->farmer ? trim($record->farmer->firstName . ' ' . $record->farmer->surname) : 'N/A'
                    )
                    ->searchable()
                    ->sortable(),
                TextColumn::make('size')
                    ->label('Size')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sizeUnit')
                    ->label('Unit')
                    ->badge(),
                TextColumn::make('ward.name')
                    ->label('Ward')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('district.name')
                    ->label('District')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('region.name')
                    ->label('Region')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('legalStatus.name')
                    ->label('Legal Status')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Farm Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
