<?php

namespace App\Filament\Resources\OtpVerifications\Pages;

use App\Filament\Resources\OtpVerifications\OtpVerificationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditOtpVerification extends EditRecord
{
    protected static string $resource = OtpVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
