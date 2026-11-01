<?php

namespace App\Filament\Concerns;

trait HasAnimalPreviewContext
{
    public string $sexCode = 'female';

    public string $unitType = 'individual';

    public string $previewLocale = 'en';

    public string $stageCode = '';

    public string $previewRole = 'farmer';

    public array $previewValues = [];

    public function updatedUnitType(): void
    {
        $this->sexCode = $this->unitType === 'group' ? 'not_applicable' : 'unknown';
    }

    protected function previewFacts(): array
    {
        return ['subject' => ['sex_code' => $this->sexCode, 'unit_type' => $this->unitType,
            'stage_code' => $this->stageCode === '' ? null : $this->stageCode, 'lifecycle_status' => 'active'],
            'user' => ['role' => $this->previewRole]];
    }
}
