<?php

namespace App\Filament\Resources\SystemUsers;

use App\Filament\Resources\SystemUsers\Pages\CreateSystemUser;
use App\Filament\Resources\SystemUsers\Pages\EditSystemUser;
use App\Filament\Resources\SystemUsers\Pages\ListSystemUsers;
use App\Filament\Resources\SystemUsers\Schemas\SystemUserForm;
use App\Filament\Resources\SystemUsers\Tables\SystemUsersTable;
use App\Models\SystemUser;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SystemUserResource extends Resource
{
    protected static ?string $model = SystemUser::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static UnitEnum|string|null $navigationGroup = 'People & Users';

    protected static ?string $navigationLabel = 'System User Profiles';

    protected static ?string $modelLabel = 'System User Profile';

    protected static ?string $pluralModelLabel = 'System User Profiles';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return SystemUserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SystemUsersTable::configure($table);
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
            'index' => ListSystemUsers::route('/'),
            'create' => CreateSystemUser::route('/create'),
            'edit' => EditSystemUser::route('/{record}/edit'),
        ];
    }
}
