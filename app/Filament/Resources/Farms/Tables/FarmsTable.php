<?php

namespace App\Filament\Resources\Farms\Tables;

use App\Models\Livestock;
use App\Models\Vaccine;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FarmsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
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
                    DeleteAction::make()
                        ->before(function ($record) {
                            // Delete all Livestocks and Vaccines before deleting Farm
                            self::deleteFarmRelatedData($record);
                        }),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function ($records) {
                            // Delete all Livestocks and Vaccines for each farm
                            foreach ($records as $farm) {
                                self::deleteFarmRelatedData($farm);
                            }
                        }),
                ]),
            ]);
    }

    /**
     * Delete all Livestocks and Vaccines associated with a farm.
     * Livestock deletion will cascade to all their logs.
     */
    protected static function deleteFarmRelatedData($farm): void
    {
        $farmUuid = $farm->uuid;

        // Get all livestocks in this farm
        $livestocks = Livestock::where('farmUuid', $farmUuid)->get();
        
        // Delete each livestock (which will cascade to all their logs via Livestock deletion)
        foreach ($livestocks as $livestock) {
            // Delete all livestock logs first
            self::deleteLivestockLogs($livestock);
            // Then delete the livestock
            $livestock->delete();
        }

        // Delete all Vaccines registered on this farm
        Vaccine::where('farmUuid', $farmUuid)->delete();
    }

    /**
     * Delete all logs/events associated with a livestock.
     */
    protected static function deleteLivestockLogs($livestock): void
    {
        $livestockUuid = $livestock->uuid;

        // Delete all 14+ log types
        \App\Models\BirthEvent::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\AbortedPregnancy::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\WeightChange::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\Vaccination::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\Treatment::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\Deworming::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\Feeding::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\Milking::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\Insemination::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\Pregnancy::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\Dryoff::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\Disposal::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\Transfer::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\Calving::where('livestockUuid', $livestockUuid)->delete();
    }
}
