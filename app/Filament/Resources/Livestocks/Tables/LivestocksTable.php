<?php

namespace App\Filament\Resources\Livestocks\Tables;

use App\Models\AbortedPregnancy;
use App\Models\BirthEvent;
use App\Models\Calving;
use App\Models\Deworming;
use App\Models\Disposal;
use App\Models\Dryoff;
use App\Models\Feeding;
use App\Models\Insemination;
use App\Models\Treatment;
use App\Models\Milking;
use App\Models\Pregnancy;
use App\Models\Transfer;
use App\Models\Vaccination;
use App\Models\WeightChange;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LivestocksTable
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
                    ->label('Livestock Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('identificationNumber')
                    ->label('ID Number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('farm.name')
                    ->label('Farm')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('species.name')
                    ->label('Species')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('breed.name')
                    ->label('Breed')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('livestockType.name')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('gender')
                    ->label('Gender')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'male' => 'info',
                        'female' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('dateOfBirth')
                    ->label('DOB')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('weightAsOnRegistration')
                    ->label('Weight (kg)')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('primaryColor')
                    ->label('Primary Color')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('secondaryColor')
                    ->label('Secondary Color')
                    ->badge()
                    ->color('warning')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
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
                            // Delete all livestock logs/events before deleting Livestock
                            self::deleteLivestockLogs($record);
                        }),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function ($records) {
                            // Delete all livestock logs for each livestock
                            foreach ($records as $livestock) {
                                self::deleteLivestockLogs($livestock);
                            }
                        }),
                ]),
            ]);
    }

    /**
     * Delete all logs/events associated with a livestock.
     */
    protected static function deleteLivestockLogs($livestock): void
    {
        $livestockUuid = $livestock->uuid;
        $deletedCount = 0;

        // Delete all 14+ log types
        $deletedCount += BirthEvent::where('livestockUuid', $livestockUuid)->delete();
        $deletedCount += AbortedPregnancy::where('livestockUuid', $livestockUuid)->delete();
        $deletedCount += WeightChange::where('livestockUuid', $livestockUuid)->delete();
        $deletedCount += Vaccination::where('livestockUuid', $livestockUuid)->delete();
        $deletedCount += Treatment::where('livestockUuid', $livestockUuid)->delete();
        $deletedCount += Deworming::where('livestockUuid', $livestockUuid)->delete();
        $deletedCount += Feeding::where('livestockUuid', $livestockUuid)->delete();
        $deletedCount += Milking::where('livestockUuid', $livestockUuid)->delete();
        $deletedCount += Insemination::where('livestockUuid', $livestockUuid)->delete();
        $deletedCount += Pregnancy::where('livestockUuid', $livestockUuid)->delete();
        $deletedCount += Dryoff::where('livestockUuid', $livestockUuid)->delete();
        $deletedCount += Disposal::where('livestockUuid', $livestockUuid)->delete();
        $deletedCount += Transfer::where('livestockUuid', $livestockUuid)->delete();
        $deletedCount += Calving::where('livestockUuid', $livestockUuid)->delete();
    }
}
