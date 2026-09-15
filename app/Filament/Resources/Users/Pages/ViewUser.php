<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    /**
     * InfoList for viewing user details (clean, read-only display)
     */
    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Information')
                    ->description('User account credentials and access details')
                    ->schema([
                        TextEntry::make('username')
                            ->label('Username')
                            ->icon('heroicon-o-user')
                            ->copyable(),
                        TextEntry::make('email')
                            ->label('Email Address')
                            ->icon('heroicon-o-envelope')
                            ->copyable(),
                        TextEntry::make('role')
                            ->label('User Role')
                            ->badge()
                            ->formatStateUsing(fn (string $state) => \App\Enums\UserRole::getDisplayName($state))
                            ->color(fn (string $state): string => match ($state) {
                                'systemUser' => 'success',
                                'farmer' => 'info',
                                'extensionOfficer' => 'warning',
                                'vet' => 'danger',
                                'farmInvitedUser' => 'primary',
                                default => 'gray',
                            }),
                        TextEntry::make('status')
                            ->label('Account Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'notActive' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    ->columns(2),

                Section::make('Profile Details')
                    ->description('Linked profile information')
                    ->schema([
                        TextEntry::make('profile_name')
                            ->label('Linked Profile Name')
                            ->getStateUsing(fn ($record) => $record->getFilamentName())
                            ->weight('bold')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('creator.username')
                            ->label('Created By')
                            ->formatStateUsing(fn ($record) => $record->creator?->getFilamentName() ?? $record->creator?->username ?? 'System')
                            ->placeholder('System'),
                        TextEntry::make('updater.username')
                            ->label('Last Updated By')
                            ->formatStateUsing(fn ($record) => $record->updater?->getFilamentName() ?? $record->updater?->username ?? 'N/A')
                            ->placeholder('N/A'),
                    ])
                    ->columns(3),

                Section::make('System Timestamps')
                    ->description('Record creation and modification dates')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('M d, Y h:i A')
                            ->icon('heroicon-o-clock'),
                        TextEntry::make('updated_at')
                            ->label('Last Updated At')
                            ->dateTime('M d, Y h:i A')
                            ->icon('heroicon-o-arrow-path'),
                    ])
                    ->columns(2),
            ]);
    }
}

