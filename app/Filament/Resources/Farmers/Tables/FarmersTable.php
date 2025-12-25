<?php

namespace App\Filament\Resources\Farmers\Tables;

use App\Enums\UserRole;
use App\Models\Farm;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FarmersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('#')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('farmerNo')
                    ->label('Farmer Number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('firstName')
                    ->label('First Name')
                    ->formatStateUsing(fn ($record) =>
                        trim($record->firstName . ' ' . ($record->middleName ?? '') . ' ' . $record->surname)
                    )
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('phone1')
                    ->label('Primary Phone')
                    ->searchable()
                    ->icon('heroicon-o-phone'),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
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
                TextColumn::make('ward.name')
                    ->label('Ward')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('district.name')
                    ->label('District')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('farmerType')
                    ->label('Type')
                    ->badge(),
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
                            // Delete all Farms (which cascades to Livestocks, Vaccines, and logs)
                            self::deleteFarmerRelatedData($record);
                            
                            // Delete linked User account before deleting Farmer
                            // Find User where roleId matches Farmer id and role is FARMER
                            $linkedUser = User::where('roleId', $record->id)
                                ->where('role', UserRole::FARMER)
                                ->first();

                            if ($linkedUser) {
                                $userEmail = $linkedUser->email ?? 'N/A';
                                $linkedUser->delete();

                                Notification::make()
                                    ->title('User account deleted')
                                    ->body("Linked login account ({$userEmail}) has been deleted.")
                                    ->success()
                                    ->send();
                            }
                        }),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function ($records) {
                            // Delete all Farms for each farmer (which cascades to Livestocks, Vaccines, and logs)
                            foreach ($records as $farmer) {
                                self::deleteFarmerRelatedData($farmer);
                            }
                            
                            // Delete linked User accounts before deleting Farmers
                            $deletedEmails = [];
                            
                            foreach ($records as $farmer) {
                                $linkedUser = User::where('roleId', $farmer->id)
                                    ->where('role', UserRole::FARMER)
                                    ->first();

                                if ($linkedUser) {
                                    if ($linkedUser->email) {
                                        $deletedEmails[] = $linkedUser->email;
                                    }
                                    $linkedUser->delete();
                                }
                            }

                            if (!empty($deletedEmails)) {
                                $emailList = implode(', ', array_unique($deletedEmails));
                                Notification::make()
                                    ->title('User accounts deleted')
                                    ->body("Linked login account(s) ({$emailList}) have been deleted for selected farmers.")
                                    ->success()
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }

    /**
     * Delete all Farms associated with a farmer.
     * Farm deletion will cascade to Livestocks, Vaccines, and all Livestock logs.
     */
    protected static function deleteFarmerRelatedData($farmer): void
    {
        $farmerId = $farmer->id;

        // Get all farms owned by this farmer
        $farms = Farm::where('farmerId', $farmerId)->get();

        // Delete each farm (which will cascade to Livestocks, Vaccines, and logs)
        foreach ($farms as $farm) {
            // Delete all Livestocks and Vaccines for this farm
            self::deleteFarmRelatedData($farm);
            // Then delete the farm
            $farm->delete();
        }
    }

    /**
     * Delete all Livestocks and Vaccines associated with a farm.
     * Livestock deletion will cascade to all their logs.
     */
    protected static function deleteFarmRelatedData($farm): void
    {
        $farmUuid = $farm->uuid;

        // Get all livestocks in this farm
        $livestocks = \App\Models\Livestock::where('farmUuid', $farmUuid)->get();
        
        // Delete each livestock (which will cascade to all their logs)
        foreach ($livestocks as $livestock) {
            // Delete all livestock logs first
            self::deleteLivestockLogs($livestock);
            // Then delete the livestock
            $livestock->delete();
        }

        // Delete all Vaccines registered on this farm
        \App\Models\Vaccine::where('farmUuid', $farmUuid)->delete();
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
