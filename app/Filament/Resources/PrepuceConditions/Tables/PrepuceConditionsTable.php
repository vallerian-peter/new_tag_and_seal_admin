<?php

namespace App\Filament\Resources\PrepuceConditions\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PrepuceConditionsTable
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
                    ->toggleable(),
                TextColumn::make('conditionType.name')
                    ->label('Condition Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('severity.name')
                    ->label('Severity')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('breedingStatus.name')
                    ->label('Breeding Status')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('healingStatus.name')
                    ->label('Healing Status')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ,
            ])
            ->filters([])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
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

