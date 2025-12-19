<?php

namespace App\Filament\Resources\OtpVerifications;

use App\Filament\Resources\OtpVerifications\Pages\CreateOtpVerification;
use App\Filament\Resources\OtpVerifications\Pages\EditOtpVerification;
use App\Filament\Resources\OtpVerifications\Pages\ListOtpVerifications;
use App\Filament\Resources\OtpVerifications\Pages\ViewOtpVerification;
use App\Filament\Resources\OtpVerifications\Schemas\OtpVerificationForm;
use App\Filament\Resources\OtpVerifications\Schemas\OtpVerificationInfolist;
use App\Filament\Resources\OtpVerifications\Tables\OtpVerificationsTable;
use App\Models\OtpVerification;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class OtpVerificationResource extends Resource
{
    protected static ?string $model = OtpVerification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static UnitEnum|string|null $navigationGroup = 'Security & Verification';

    protected static ?string $navigationLabel = 'OTP Verifications';

    protected static ?string $modelLabel = 'OTP Verification';

    protected static ?string $pluralModelLabel = 'OTP Verifications';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return OtpVerificationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OtpVerificationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OtpVerificationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOtpVerifications::route('/'),
            'create' => CreateOtpVerification::route('/create'),
            'view' => ViewOtpVerification::route('/{record}'),
            'edit' => EditOtpVerification::route('/{record}/edit'),
        ];
    }
}
