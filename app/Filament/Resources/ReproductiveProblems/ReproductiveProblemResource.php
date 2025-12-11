<?php

namespace App\Filament\Resources\ReproductiveProblems;

use App\Filament\Resources\ReproductiveProblems\Pages\CreateReproductiveProblem;
use App\Filament\Resources\ReproductiveProblems\Pages\EditReproductiveProblem;
use App\Filament\Resources\ReproductiveProblems\Pages\ListReproductiveProblems;
use App\Filament\Resources\ReproductiveProblems\Schemas\ReproductiveProblemForm;
use App\Filament\Resources\ReproductiveProblems\Tables\ReproductiveProblemsTable;
use App\Models\ReproductiveProblem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class ReproductiveProblemResource extends Resource
{
    protected static ?string $model = ReproductiveProblem::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-circle';

    protected static UnitEnum|string|null $navigationGroup = 'Logs Reference Data';

    protected static ?string $navigationLabel = 'Reproductive Problems';

    protected static ?string $modelLabel = 'Reproductive Problem';

    protected static ?string $pluralModelLabel = 'Reproductive Problems';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return ReproductiveProblemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReproductiveProblemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReproductiveProblems::route('/'),
            'create' => CreateReproductiveProblem::route('/create'),
            'edit' => EditReproductiveProblem::route('/{record}/edit'),
        ];
    }
}

