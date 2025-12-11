<?php

namespace App\Filament\Resources\LegalStatuses;

use App\Filament\Resources\LegalStatuses\Pages\CreateLegalStatus;
use App\Filament\Resources\LegalStatuses\Pages\EditLegalStatus;
use App\Filament\Resources\LegalStatuses\Pages\ListLegalStatuses;
use App\Filament\Resources\LegalStatuses\Schemas\LegalStatusForm;
use App\Filament\Resources\LegalStatuses\Tables\LegalStatusesTable;
use App\Models\LegalStatus;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LegalStatusResource extends Resource
{
    protected static ?string $model = LegalStatus::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static UnitEnum|string|null $navigationGroup = 'System & Configuration';

    protected static ?string $navigationLabel = 'Legal Statuses';

    protected static ?string $modelLabel = 'Legal Status';

    protected static ?string $pluralModelLabel = 'Legal Statuses';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return LegalStatusForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LegalStatusesTable::configure($table);
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
            'index' => ListLegalStatuses::route('/'),
            'create' => CreateLegalStatus::route('/create'),
            'edit' => EditLegalStatus::route('/{record}/edit'),
        ];
    }
}
