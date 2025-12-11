<?php

namespace App\Filament\Resources\FarmUsers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FarmUsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('firstName')
                    ->label('Full Name')
                    ->formatStateUsing(
                        fn ($record) => trim(
                            $record->firstName . ' ' .
                            ($record->middleName ?? '') . ' ' .
                            $record->lastName
                        )
                    )
                    ->searchable(['firstName', 'middleName', 'lastName'])
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('farm.name')
                    ->label('Farm')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roleTitle')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => str_replace('-', ' ', ucfirst($state)))
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->icon('heroicon-o-phone'),
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('gender')
                    ->label('Gender')
                    ->badge()
                    ->sortable(),
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


