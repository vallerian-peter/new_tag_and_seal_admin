<?php

namespace App\Filament\Resources\OtpVerifications\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class OtpVerificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('#')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-envelope')
                    ->toggleable(),
                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-phone')
                    ->toggleable(),
                TextColumn::make('otp')
                    ->label('OTP Code')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->copyable()
                    ->weight('bold'),
                TextColumn::make('expires_at')
                    ->label('Expires At')
                    ->dateTime()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->isExpired() ? 'danger' : 'success')
                    ->formatStateUsing(fn ($state, $record) => 
                        $state->format('Y-m-d H:i') . ($record->isExpired() ? ' (Expired)' : ' (Active)')
                    ),
                IconColumn::make('verified')
                    ->label('Verified')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($record) => 
                        $record->verified ? 'Verified' : 
                        ($record->isExpired() ? 'Expired' : 'Active')
                    )
                    ->color(fn ($record) => 
                        $record->verified ? 'success' : 
                        ($record->isExpired() ? 'danger' : 'warning')
                    )
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('verified', $direction)
                            ->orderBy('expires_at', $direction);
                    }),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('verified')
                    ->label('Verified Status')
                    ->placeholder('All')
                    ->trueLabel('Verified')
                    ->falseLabel('Not Verified'),
                SelectFilter::make('status')
                    ->label('OTP Status')
                    ->options([
                        'active' => 'Active',
                        'expired' => 'Expired',
                        'verified' => 'Verified',
                    ])
                    ->query(function ($query, $state) {
                        if ($state['value'] === 'active') {
                            return $query->where('verified', false)
                                ->where('expires_at', '>', now());
                        } elseif ($state['value'] === 'expired') {
                            return $query->where('verified', false)
                                ->where('expires_at', '<=', now());
                        } elseif ($state['value'] === 'verified') {
                            return $query->where('verified', true);
                        }
                        return $query;
                    }),
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
