<?php

namespace App\Filament\Resources\ExtensionOfficerFarmInvites\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExtensionOfficerFarmInvitesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('#')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('extensionOfficer.full_name')
                    ->label('Extension Officer')
                    ->formatStateUsing(fn ($record) => 
                        trim($record->extensionOfficer->firstName . ' ' . 
                             ($record->extensionOfficer->middleName ?? '') . ' ' . 
                             $record->extensionOfficer->lastName)
                    )
                    ->searchable(query: function ($query, $search) {
                        return $query->whereHas('extensionOfficer', function ($q) use ($search) {
                            $q->whereRaw("CONCAT(firstName, ' ', COALESCE(middleName, ''), ' ', lastName) LIKE ?", ["%{$search}%"]);
                        });
                    })
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('farmer.full_name')
                    ->label('Farmer / Owner')
                    ->formatStateUsing(fn ($record) => 
                        trim($record->farmer->firstName . ' ' . 
                             ($record->farmer->middleName ?? '') . ' ' . 
                             $record->farmer->surname)
                    )
                    ->searchable(query: function ($query, $search) {
                        return $query->whereHas('farmer', function ($q) use ($search) {
                            $q->whereRaw("CONCAT(firstName, ' ', COALESCE(middleName, ''), ' ', surname) LIKE ?", ["%{$search}%"]);
                        });
                    })
                    ->sortable(),
                TextColumn::make('farmer.email')
                    ->label('Owner Email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('access_code')
                    ->label('Access Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Access code copied!')
                    ->copyMessageDuration(1500)
                    ->weight('bold')
                    ->badge()
                    ->color('success'),
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
