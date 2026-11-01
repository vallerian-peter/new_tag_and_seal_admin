<?php

namespace App\Filament\Resources\FormDefinitions\Pages;

use App\Filament\Concerns\HasAnimalPreviewContext;
use App\Filament\Resources\FormDefinitions\FormDefinitionResource;
use App\Services\Preview\AnimalFlowPreview;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class PreviewFormDefinition extends Page
{
    use HasAnimalPreviewContext;
    use InteractsWithRecord;

    protected static string $resource = FormDefinitionResource::class;

    protected string $view = 'filament.previews.form-definition';

    public function mount(int|string $record): void
    {
        abort_unless(FormDefinitionResource::canViewAny(), 403);
        $this->record = $this->resolveRecord($record);
    }

    public function getTitle(): string
    {
        return 'Preview form';
    }

    protected function getViewData(): array
    {
        abort_unless(FormDefinitionResource::canViewAny(), 403);

        return ['form' => app(AnimalFlowPreview::class)->form($this->getRecord(), $this->previewFacts(), $this->previewLocale, $this->previewValues)];
    }
}
