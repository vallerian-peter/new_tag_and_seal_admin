<?php

namespace App\Filament\Resources\OtpVerifications\Pages;

use App\Filament\Resources\OtpVerifications\OtpVerificationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOtpVerifications extends ListRecords
{
    protected static string $resource = OtpVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
