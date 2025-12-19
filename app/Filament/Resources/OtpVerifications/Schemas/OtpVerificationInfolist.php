<?php

namespace App\Filament\Resources\OtpVerifications\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OtpVerificationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('email')
                    ->label('Email Address')
                    ->icon('heroicon-o-envelope')
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->label('Phone Number')
                    ->icon('heroicon-o-phone')
                    ->placeholder('-'),
                TextEntry::make('otp')
                    ->label('OTP Code')
                    ->badge()
                    ->color('info')
                    ->copyable(),
                TextEntry::make('expires_at')
                    ->label('Expires At')
                    ->dateTime()
                    ->badge()
                    ->color(fn ($record) => $record->isExpired() ? 'danger' : 'success')
                    ->formatStateUsing(fn ($state, $record) => 
                        $state->format('Y-m-d H:i:s') . ($record->isExpired() ? ' (Expired)' : ' (Active)')
                    ),
                IconEntry::make('verified')
                    ->label('Verified')
                    ->boolean(),
                TextEntry::make('status')
                    ->label('Current Status')
                    ->badge()
                    ->formatStateUsing(fn ($record) => 
                        $record->verified ? 'Verified' : 
                        ($record->isExpired() ? 'Expired' : 'Active')
                    )
                    ->color(fn ($record) => 
                        $record->verified ? 'success' : 
                        ($record->isExpired() ? 'danger' : 'warning')
                    ),
                TextEntry::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
