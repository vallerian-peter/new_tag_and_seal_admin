<?php

namespace App\Filament\Resources\IdentityCardTypes;

use App\Filament\Resources\IdentityCardTypes\Pages\CreateIdentityCardType;
use App\Filament\Resources\IdentityCardTypes\Pages\EditIdentityCardType;
use App\Filament\Resources\IdentityCardTypes\Pages\ListIdentityCardTypes;
use App\Filament\Resources\IdentityCardTypes\Schemas\IdentityCardTypeForm;
use App\Filament\Resources\IdentityCardTypes\Tables\IdentityCardTypesTable;
use App\Models\IdentityCardType;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class IdentityCardTypeResource extends Resource
{
    protected static ?string $model = IdentityCardType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static UnitEnum|string|null $navigationGroup = 'System & Configuration';

    protected static ?string $navigationLabel = 'ID Card Types';

    protected static ?string $modelLabel = 'Identity Card Type';

    protected static ?string $pluralModelLabel = 'Identity Card Types';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return IdentityCardTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IdentityCardTypesTable::configure($table);
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
            'index' => ListIdentityCardTypes::route('/'),
            'create' => CreateIdentityCardType::route('/create'),
            'edit' => EditIdentityCardType::route('/{record}/edit'),
        ];
    }
}
