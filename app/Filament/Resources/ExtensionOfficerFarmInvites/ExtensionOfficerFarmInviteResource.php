<?php

namespace App\Filament\Resources\ExtensionOfficerFarmInvites;

use App\Filament\Resources\ExtensionOfficerFarmInvites\Pages\CreateExtensionOfficerFarmInvite;
use App\Filament\Resources\ExtensionOfficerFarmInvites\Pages\EditExtensionOfficerFarmInvite;
use App\Filament\Resources\ExtensionOfficerFarmInvites\Pages\ListExtensionOfficerFarmInvites;
use App\Filament\Resources\ExtensionOfficerFarmInvites\Pages\ViewExtensionOfficerFarmInvite;
use App\Filament\Resources\ExtensionOfficerFarmInvites\Schemas\ExtensionOfficerFarmInviteForm;
use App\Filament\Resources\ExtensionOfficerFarmInvites\Schemas\ExtensionOfficerFarmInviteInfolist;
use App\Filament\Resources\ExtensionOfficerFarmInvites\Tables\ExtensionOfficerFarmInvitesTable;
use App\Models\ExtensionOfficerFarmInvite;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ExtensionOfficerFarmInviteResource extends Resource
{
    protected static ?string $model = ExtensionOfficerFarmInvite::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static UnitEnum|string|null $navigationGroup = 'People & Users';

    protected static ?string $navigationLabel = 'Farm Invites';

    protected static ?string $modelLabel = 'Farm Invite';

    protected static ?string $pluralModelLabel = 'Farm Invites';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return ExtensionOfficerFarmInviteForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExtensionOfficerFarmInviteInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExtensionOfficerFarmInvitesTable::configure($table);
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
            'index' => ListExtensionOfficerFarmInvites::route('/'),
            'create' => CreateExtensionOfficerFarmInvite::route('/create'),
            'view' => ViewExtensionOfficerFarmInvite::route('/{record}'),
            'edit' => EditExtensionOfficerFarmInvite::route('/{record}/edit'),
        ];
    }
}
