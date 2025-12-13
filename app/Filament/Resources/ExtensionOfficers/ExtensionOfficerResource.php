<?php

namespace App\Filament\Resources\ExtensionOfficers;

use App\Filament\Resources\ExtensionOfficers\Pages\CreateExtensionOfficer;
use App\Filament\Resources\ExtensionOfficers\Pages\EditExtensionOfficer;
use App\Filament\Resources\ExtensionOfficers\Pages\ListExtensionOfficers;
use App\Filament\Resources\ExtensionOfficers\Pages\ViewExtensionOfficer;
use App\Filament\Resources\ExtensionOfficers\Schemas\ExtensionOfficerForm;
use App\Filament\Resources\ExtensionOfficers\Schemas\ExtensionOfficerInfolist;
use App\Filament\Resources\ExtensionOfficers\Tables\ExtensionOfficersTable;
use App\Models\ExtensionOfficer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ExtensionOfficerResource extends Resource
{
    protected static ?string $model = ExtensionOfficer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static UnitEnum|string|null $navigationGroup = 'People & Users';

    protected static ?string $navigationLabel = 'Extension Officers';

    protected static ?string $modelLabel = 'Extension Officer';

    protected static ?string $pluralModelLabel = 'Extension Officers';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return ExtensionOfficerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExtensionOfficerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExtensionOfficersTable::configure($table);
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
            'index' => ListExtensionOfficers::route('/'),
            'create' => CreateExtensionOfficer::route('/create'),
            'view' => ViewExtensionOfficer::route('/{record}'),
            'edit' => EditExtensionOfficer::route('/{record}/edit'),
        ];
    }
}
