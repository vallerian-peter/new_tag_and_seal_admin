<?php

namespace App\Filament\Resources\OtpVerifications\Pages;

use App\Filament\Resources\OtpVerifications\OtpVerificationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOtpVerification extends ViewRecord
{
    protected static string $resource = OtpVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
