<?php

namespace App\Filament\Resources\BirthProblems;

use App\Filament\Resources\BirthProblems\Pages\CreateBirthProblem;
use App\Filament\Resources\BirthProblems\Pages\EditBirthProblem;
use App\Filament\Resources\BirthProblems\Pages\ListBirthProblems;
use App\Filament\Resources\BirthProblems\Schemas\BirthProblemForm;
use App\Filament\Resources\BirthProblems\Tables\BirthProblemsTable;
use App\Models\BirthProblem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class BirthProblemResource extends Resource
{
    protected static ?string $model = BirthProblem::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Birth Problems';

    protected static ?string $modelLabel = 'Birth Problem';

    protected static ?string $pluralModelLabel = 'Birth Problems';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return BirthProblemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BirthProblemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBirthProblems::route('/'),
            'create' => CreateBirthProblem::route('/create'),
            'edit' => EditBirthProblem::route('/{record}/edit'),
        ];
    }
}

