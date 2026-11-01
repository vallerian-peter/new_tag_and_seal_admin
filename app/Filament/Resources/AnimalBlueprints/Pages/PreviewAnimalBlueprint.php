<?php

namespace App\Filament\Resources\AnimalBlueprints\Pages;

use App\Filament\Concerns\HasAnimalPreviewContext;
use App\Filament\Resources\AnimalBlueprints\AnimalBlueprintResource;
use App\Services\Preview\AnimalFlowPreview;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class PreviewAnimalBlueprint extends Page
{
    use HasAnimalPreviewContext;
    use InteractsWithRecord;

    protected static string $resource = AnimalBlueprintResource::class;

    protected string $view = 'filament.previews.animal-flow';

    public function mount(int|string $record): void
    {
        abort_unless(AnimalBlueprintResource::canViewAny(), 403);
        $this->record = $this->resolveRecord($record);
        $modes = $this->record->settings_json['subjectModes'] ?? ['individual'];
        $this->unitType = in_array('individual', $modes, true) ? 'individual' : ($modes[0] ?? 'individual');
        if ($this->unitType === 'group') {
            $this->sexCode = 'not_applicable';
        }
    }

    public function getTitle(): string
    {
        return 'Preview animal forms and events';
    }

    protected function getViewData(): array
    {
        abort_unless(AnimalBlueprintResource::canViewAny(), 403);

        return ['flow' => app(AnimalFlowPreview::class)->blueprint($this->getRecord(), $this->previewFacts(), $this->previewLocale, $this->previewValues)];
    }
}
