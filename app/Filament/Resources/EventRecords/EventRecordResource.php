<?php

namespace App\Filament\Resources\EventRecords;

use App\Filament\Resources\EventRecords\Pages\ListEventRecords;
use App\Filament\Resources\EventRecords\Tables\EventRecordsTable;
use App\Models\EventRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/** Read-only explorer for ledger events; values render through the form version they were saved with. */
class EventRecordResource extends Resource
{
    protected static ?string $model = EventRecord::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected static UnitEnum|string|null $navigationGroup = 'Events & Logs';

    protected static ?string $navigationLabel = 'Events & Logs';

    protected static ?string $modelLabel = 'Event';

    protected static ?string $pluralModelLabel = 'Events & Logs';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'event_code';

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return static::getUrl('index');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['event_code', 'subject.display_name'];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPlatformPermission('subjects.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return EventRecordsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEventRecords::route('/'),
            'view' => Pages\ViewEventRecord::route('/{record}'),
        ];
    }
}
