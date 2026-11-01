<?php

namespace App\Filament\Resources\AnimalSubjects\Pages;

use App\Filament\Resources\AnimalSubjects\AnimalSubjectResource;
use App\Models\AnimalSubject;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListAnimalSubjects extends ListRecords
{
    protected static string $resource = AnimalSubjectResource::class;

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
        return AnimalSubject::query()->whereNull('deleted_at')->count();
    }

    public function getArchivedCount(): int
    {
        return AnimalSubject::onlyTrashed()->count();
    }

    protected function getTableQuery(): ?Builder
    {
        $query = parent::getTableQuery();

        if ($query && $this->recordView === 'archived') {
            $query->onlyTrashed();
        }

        return $query;
    }
}
