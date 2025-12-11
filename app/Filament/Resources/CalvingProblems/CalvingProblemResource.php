<?php

namespace App\Filament\Resources\CalvingProblems;

use App\Filament\Resources\CalvingProblems\Pages\CreateCalvingProblem;
use App\Filament\Resources\CalvingProblems\Pages\EditCalvingProblem;
use App\Filament\Resources\CalvingProblems\Pages\ListCalvingProblems;
use App\Filament\Resources\CalvingProblems\Schemas\CalvingProblemForm;
use App\Filament\Resources\CalvingProblems\Tables\CalvingProblemsTable;
use App\Models\CalvingProblem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class CalvingProblemResource extends Resource
{
    protected static ?string $model = CalvingProblem::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Calving Problems';

    protected static ?string $modelLabel = 'Calving Problem';

    protected static ?string $pluralModelLabel = 'Calving Problems';

    protected static ?int $navigationSort = 11;

    // Hide from navigation - use BirthProblems instead
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return CalvingProblemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CalvingProblemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCalvingProblems::route('/'),
            'create' => CreateCalvingProblem::route('/create'),
            'edit' => EditCalvingProblem::route('/{record}/edit'),
        ];
    }
}

