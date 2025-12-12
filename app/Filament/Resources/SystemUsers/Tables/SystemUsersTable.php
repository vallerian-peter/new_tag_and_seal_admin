<?php

namespace App\Filament\Resources\SystemUsers\Tables;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SystemUsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('#')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('firstName')
                    ->label('Full Name')
                    ->formatStateUsing(fn ($record) =>
                        trim($record->firstName . ' ' . ($record->middleName ?? '') . ' ' . $record->lastName)
                    )
                    ->searchable(['firstName', 'middleName', 'lastName'])
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('phone')
                    ->label('Phone Number')
                    ->searchable()
                    ->icon('heroicon-o-phone'),
                TextColumn::make('address')
                    ->label('Address')
                    ->searchable()
                    ->limit(50)
                    ->toggleable(),
                TextColumn::make('createdBy')
                    ->label('Created By')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                        // Check if this SystemUser has an admin (SYSTEM_USER) account
                        $adminUser = User::where('roleId', $record->id)
                            ->where('role', UserRole::SYSTEM_USER)
                            ->first();

                        if ($adminUser) {
                            // Count total admin users
                            $totalAdmins = User::where('role', UserRole::SYSTEM_USER)->count();

                            // Prevent deletion if this would leave less than 2 admins
                            if ($totalAdmins <= 1) {
                                Notification::make()
                                    ->title('Cannot delete admin')
                                    ->body('You cannot delete the last admin user. At least 2 admin users must exist in the system.')
                                    ->danger()
                                    ->persistent()
                                    ->send();

                                // Prevent deletion by throwing an exception
                                throw new \Exception('Cannot delete the last admin user.');
                            }
                        }

                        // Delete linked User accounts before deleting SystemUser
                        // Find Users where roleId matches SystemUser id and role uses SystemUser profile
                        $linkedUsers = User::where('roleId', $record->id)
                            ->whereIn('role', [
                                UserRole::SYSTEM_USER,
                                UserRole::EXTENSION_OFFICER,
                                UserRole::VET,
                            ])
                            ->get();

                        if ($linkedUsers->isNotEmpty()) {
                            $emails = $linkedUsers->pluck('email')->filter()->toArray();
                            
                            foreach ($linkedUsers as $user) {
                                $user->delete();
                            }

                            $emailList = !empty($emails) ? implode(', ', $emails) : 'N/A';
                            Notification::make()
                                ->title('User accounts deleted')
                                ->body("Linked login account(s) ({$emailList}) have been deleted.")
                                ->success()
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function ($records) {
                            // Check how many admin users would be deleted
                            $adminSystemUserIds = [];
                            foreach ($records as $systemUser) {
                                $adminUser = User::where('roleId', $systemUser->id)
                                    ->where('role', UserRole::SYSTEM_USER)
                                    ->first();
                                
                                if ($adminUser) {
                                    $adminSystemUserIds[] = $systemUser->id;
                                }
                            }

                            // Count total admin users
                            $totalAdmins = User::where('role', UserRole::SYSTEM_USER)->count();
                            $adminsToDelete = count($adminSystemUserIds);
                            $adminsRemaining = $totalAdmins - $adminsToDelete;

                            // Prevent deletion if this would leave less than 2 admins
                            if ($adminsRemaining < 2) {
                                Notification::make()
                                    ->title('Cannot delete admin users')
                                    ->body("Deleting these system users would leave only {$adminsRemaining} admin user(s). At least 2 admin users must exist in the system.")
                                    ->danger()
                                    ->persistent()
                                    ->send();

                                // Prevent deletion by throwing an exception
                                throw new \Exception('Cannot delete system users that would leave less than 2 admin users.');
                            }

                            // Delete linked User accounts before deleting SystemUsers
                            $deletedEmails = [];
                            
                            foreach ($records as $systemUser) {
                                $linkedUsers = User::where('roleId', $systemUser->id)
                                    ->whereIn('role', [
                                        UserRole::SYSTEM_USER,
                                        UserRole::EXTENSION_OFFICER,
                                        UserRole::VET,
                                    ])
                                    ->get();

                                foreach ($linkedUsers as $user) {
                                    if ($user->email) {
                                        $deletedEmails[] = $user->email;
                                    }
                                    $user->delete();
                                }
                            }

                            if (!empty($deletedEmails)) {
                                $emailList = implode(', ', array_unique($deletedEmails));
                                Notification::make()
                                    ->title('User accounts deleted')
                                    ->body("Linked login account(s) ({$emailList}) have been deleted for selected system users.")
                                    ->success()
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }
}
