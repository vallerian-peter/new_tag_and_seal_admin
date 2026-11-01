<?php

namespace App\Filament\Resources\AnimalSubjects;

use App\Filament\Resources\AnimalSubjects\Pages\ListAnimalSubjects;
use App\Filament\Resources\AnimalSubjects\Pages\ViewAnimalSubject;
use App\Filament\Resources\AnimalSubjects\Tables\AnimalSubjectsTable;
use App\Models\AnimalSubject;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class AnimalSubjectResource extends Resource
{
    protected static ?string $model = AnimalSubject::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static UnitEnum|string|null $navigationGroup = 'Animals & Livestock';

    protected static ?string $navigationLabel = 'Animals';

    protected static ?string $modelLabel = 'Animal';

    protected static ?string $pluralModelLabel = 'Animals';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'display_name';

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return static::getUrl('index');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['display_name'];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPlatformPermission('subjects.view') ?? false;
    }

    public static function table(Table $table): Table
    {
        return AnimalSubjectsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if (! (auth()->user()?->hasPlatformPermission('records.restore') ?? false)) {
            // Enforce visibility even if a caller tampers with the trash filter.
            $query->whereNull('animal_subjects.deleted_at');
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnimalSubjects::route('/'),
            'view' => ViewAnimalSubject::route('/{record}'),
        ];
    }
}
