<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UserRole;
use App\Filament\Resources\ExtensionOfficers\ExtensionOfficerResource;
use App\Filament\Resources\Farmers\FarmerResource;
use App\Filament\Resources\FarmUsers\FarmUserResource;
use App\Filament\Resources\SystemUsers\SystemUserResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\ExtensionOfficer;
use App\Models\Farmer;
use App\Models\FarmUser;
use App\Models\SystemUser;
use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
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
                    ->label('Full Name')
                    ->getStateUsing(fn (User $record) => $record->getFilamentName())
                    ->weight('bold')
                    ->searchable(false)
                    ->sortable(false),
                TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->label('User Role')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => UserRole::getDisplayName($state))
                    ->searchable()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'systemUser' => 'success',
                        'farmer' => 'info',
                        'extensionOfficer' => 'warning',
                        'vet' => 'danger',
                        'farmInvitedUser' => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'notActive' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ,
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
                    ViewAction::make()
                        ->url(function (User $record): ?string {
                            try {
                                if ($record->roleId) {
                                    switch ($record->role) {
                                        case UserRole::FARMER:
                                            if (Farmer::where('id', $record->roleId)->exists()) {
                                                return FarmerResource::getUrl('view', ['record' => $record->roleId]);
                                            }
                                            break;
                                        case UserRole::EXTENSION_OFFICER:
                                            if (ExtensionOfficer::where('id', $record->roleId)->exists()) {
                                                return ExtensionOfficerResource::getUrl('view', ['record' => $record->roleId]);
                                            }
                                            break;
                                        case UserRole::FARM_INVITED_USER:
                                            if (FarmUser::where('id', $record->roleId)->exists()) {
                                                return FarmUserResource::getUrl('view', ['record' => $record->roleId]);
                                            }
                                            break;
                                        case UserRole::SYSTEM_USER:
                                        case UserRole::VET:
                                            if (SystemUser::where('id', $record->roleId)->exists()) {
                                                return SystemUserResource::getUrl('view', ['record' => $record->roleId]);
                                            }
                                            break;
                                    }
                                }
                            } catch (\Throwable $e) {
                                // Fallback
                            }
                            return UserResource::getUrl('view', ['record' => $record]);
                        }),
                    EditAction::make()
                        ->url(function (User $record): ?string {
                            try {
                                if ($record->roleId) {
                                    switch ($record->role) {
                                        case UserRole::FARMER:
                                            if (Farmer::where('id', $record->roleId)->exists()) {
                                                return FarmerResource::getUrl('edit', ['record' => $record->roleId]);
                                            }
                                            break;
                                        case UserRole::EXTENSION_OFFICER:
                                            if (ExtensionOfficer::where('id', $record->roleId)->exists()) {
                                                return ExtensionOfficerResource::getUrl('edit', ['record' => $record->roleId]);
                                            }
                                            break;
                                        case UserRole::FARM_INVITED_USER:
                                            if (FarmUser::where('id', $record->roleId)->exists()) {
                                                return FarmUserResource::getUrl('edit', ['record' => $record->roleId]);
                                            }
                                            break;
                                        case UserRole::SYSTEM_USER:
                                        case UserRole::VET:
                                            if (SystemUser::where('id', $record->roleId)->exists()) {
                                                return SystemUserResource::getUrl('edit', ['record' => $record->roleId]);
                                            }
                                            break;
                                    }
                                }
                            } catch (\Throwable $e) {
                                // Fallback
                            }
                            return UserResource::getUrl('edit', ['record' => $record]);
                        }),
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
