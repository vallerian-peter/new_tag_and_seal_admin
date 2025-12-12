<?php

namespace App\Filament\Resources\FarmUsers\Tables;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FarmUsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('farm'))
            ->defaultSort('created_at', 'desc')
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
                    ->formatStateUsing(function ($record) {
                        if (!$record->relationLoaded('farm')) {
                            $record->load('farm');
                        }
                        return $record->farm?->name ?? 'N/A';
                    })
                    ->searchable(query: fn ($query, $search) => 
                        $query->whereHas('farm', fn ($q) => 
                            $q->where('name', 'like', "%{$search}%")
                        )
                    )
                    ->sortable(query: fn ($query, $direction) => 
                        $query->join('farms', 'farm_users.farmUuid', '=', 'farms.uuid')
                            ->orderBy('farms.name', $direction)
                            ->select('farm_users.*')
                    ),
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
                DeleteAction::make()
                    ->before(function ($record) {
                        // Delete linked User account before deleting FarmUser
                        $linkedUser = User::where('role', UserRole::FARM_INVITED_USER)
                            ->where('roleId', $record->id)
                            ->first();

                        if ($linkedUser) {
                            $userEmail = $linkedUser->email;
                            $linkedUser->delete();

                            Notification::make()
                                ->title('User account deleted')
                                ->body("Linked login account ({$userEmail}) has been deleted.")
                                ->success()
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function ($records) {
                            // Delete linked User accounts before deleting FarmUsers
                            foreach ($records as $farmUser) {
                                $linkedUser = User::where('role', UserRole::FARM_INVITED_USER)
                                    ->where('roleId', $farmUser->id)
                                    ->first();

                                if ($linkedUser) {
                                    $linkedUser->delete();
                                }
                            }

                            Notification::make()
                                ->title('User accounts deleted')
                                ->body('Linked login accounts have been deleted for selected farm users.')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }
}


