<?php

namespace App\Filament\Resources\FarmUsers;

use App\Filament\Resources\FarmUsers\Pages\CreateFarmUser;
use App\Filament\Resources\FarmUsers\Pages\EditFarmUser;
use App\Filament\Resources\FarmUsers\Pages\ListFarmUsers;
use App\Filament\Resources\FarmUsers\Schemas\FarmUserForm;
use App\Filament\Resources\FarmUsers\Tables\FarmUsersTable;
use App\Models\FarmUser;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class FarmUserResource extends Resource
{
    protected static ?string $model = FarmUser::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user';

    protected static UnitEnum|string|null $navigationGroup = 'People & Users';

    protected static ?string $navigationLabel = 'Farm Users';

    protected static ?string $modelLabel = 'Farm User';

    protected static ?string $pluralModelLabel = 'Farm Users';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return FarmUserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FarmUsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFarmUsers::route('/'),
            'create' => CreateFarmUser::route('/create'),
            'edit' => EditFarmUser::route('/{record}/edit'),
        ];
    }
}


