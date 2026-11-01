<?php

namespace App\Filament\Resources\EventRecords\Pages;

use App\Filament\Resources\EventRecords\EventRecordResource;
use App\Models\EventRecord;
use App\Services\DynamicEventSchemaService;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListEventRecords extends ListRecords
{
    protected static string $resource = EventRecordResource::class;

    /**
     * View mode: 'active' (default) or 'archived'.
     */
    public string $recordView = 'active';

    public function setRecordView(string $view): void
    {
        $this->recordView = in_array($view, ['active', 'archived'], true) ? $view : 'active';
        $this->resetPage();
    }

    public function getActiveCount(): int
    {
        return EventRecord::query()
            ->where('record_status', '!=', 'voided')
            ->when($this->activeTab, fn ($q) => $q->where('event_code', $this->activeTab))
            ->count();
    }

    public function getArchivedCount(): int
    {
        return EventRecord::query()
            ->where('record_status', 'voided')
            ->when($this->activeTab, fn ($q) => $q->where('event_code', $this->activeTab))
            ->count();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return null;
    }

    protected function getTableQuery(): ?Builder
    {
        $query = parent::getTableQuery();

        if ($query) {
            $isArchived = ($this->recordView === 'archived');
            $query->when($isArchived, fn ($q) => $q->where('record_status', 'voided'))
                  ->when(! $isArchived, fn ($q) => $q->where('record_status', '!=', 'voided'));

            if ($this->activeTab) {
                $query->where('event_code', $this->activeTab);
            }
        }

        return $query;
    }

    public function getTabs(): array
    {
        $schemaService = app(DynamicEventSchemaService::class);
        $eventTypes = $schemaService->getEventTypes();
        $isArchived = ($this->recordView === 'archived');

        $baseQuery = EventRecord::query()
            ->when($isArchived, fn ($q) => $q->where('record_status', 'voided'))
            ->when(! $isArchived, fn ($q) => $q->where('record_status', '!=', 'voided'));

        // Fetch counts for each event type respecting the active/archived toggle
        $counts = (clone $baseQuery)
            ->selectRaw('event_code, count(*) as aggregate')
            ->groupBy('event_code')
            ->pluck('aggregate', 'event_code');

        $tabs = [];
        foreach ($eventTypes as $code => $info) {
            $count = (int) ($counts[$code] ?? 0);
            $tabs[$code] = Tab::make($info['name'])
                ->badge($count > 0 ? (string) $count : null)
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('event_code', $code)
                    ->when($isArchived, fn ($q) => $q->where('record_status', 'voided'))
                    ->when(! $isArchived, fn ($q) => $q->where('record_status', '!=', 'voided'))
                );
        }

        return $tabs;
    }
}
