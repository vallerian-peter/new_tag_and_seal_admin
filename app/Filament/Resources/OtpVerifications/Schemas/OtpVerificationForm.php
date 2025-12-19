<?php

namespace App\Filament\Resources\OtpVerifications\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class OtpVerificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->maxLength(255)
                    ->helperText('Email address for OTP verification'),
                TextInput::make('phone')
                    ->label('Phone Number')
                    ->tel()
                    ->maxLength(255)
                    ->helperText('Phone number for OTP verification'),
                TextInput::make('otp')
                    ->label('OTP Code')
                    ->required()
                    ->maxLength(6)
                    ->minLength(6)
                    ->numeric()
                    ->default(fn () => \App\Models\OtpVerification::generateOtp())
                    ->helperText('6-digit OTP code'),
                DateTimePicker::make('expires_at')
                    ->label('Expires At')
                    ->required()
                    ->default(now()->addMinutes(10))
                    ->native(false)
                    ->seconds(false)
                    ->helperText('OTP expiration time (default: 10 minutes from now)'),
                Toggle::make('verified')
                    ->label('Verified')
                    ->default(false)
                    ->helperText('Mark as verified if OTP has been used'),
            ]);
    }
}
