<?php

namespace App\Filament\Resources\TestResults;

use App\Filament\Resources\TestResults\Pages\CreateTestResult;
use App\Filament\Resources\TestResults\Pages\EditTestResult;
use App\Filament\Resources\TestResults\Pages\ListTestResults;
use App\Filament\Resources\TestResults\Schemas\TestResultForm;
use App\Filament\Resources\TestResults\Tables\TestResultsTable;
use App\Models\TestResults;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class TestResultResource extends Resource
{
    protected static ?string $model = TestResults::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Pregnancy Test Results';

    protected static ?string $modelLabel = 'Test Result';

    protected static ?string $pluralModelLabel = 'Test Results';

    protected static ?int $navigationSort = 12;

    public static function form(Schema $schema): Schema
    {
        return TestResultForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TestResultsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTestResults::route('/'),
            'create' => CreateTestResult::route('/create'),
            'edit' => EditTestResult::route('/{record}/edit'),
        ];
    }
}

