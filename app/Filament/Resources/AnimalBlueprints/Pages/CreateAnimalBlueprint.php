<?php

namespace App\Filament\Resources\AnimalBlueprints\Pages;

use App\Filament\Resources\AnimalBlueprints\AnimalBlueprintResource;
use App\Models\AnimalBlueprint;
use App\Models\EventDefinition;
use App\Models\User;
use App\Services\DynamicConfigurationService;
use Filament\Resources\Pages\CreateRecord;

class CreateAnimalBlueprint extends CreateRecord
{
    protected static string $resource = AnimalBlueprintResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_uuid'] = auth()->user()?->uuid ?? User::first()?->uuid;

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var AnimalBlueprint $blueprint */
        $blueprint = $this->record;
        $rawData = $this->form->getRawState();

        $selectedEvents = $rawData['initial_events'] ?? [];
        $mode = $rawData['event_setup_mode'] ?? 'universal';
        $cloneForm = ($mode === 'clone');

        if (! empty($selectedEvents)) {
            $configService = app(DynamicConfigurationService::class);
            $actor = auth()->user() ?? User::first();

            foreach ($selectedEvents as $eventCode) {
                $sourceEvent = EventDefinition::where('code', $eventCode)->first();
                if (! $sourceEvent) {
                    continue;
                }

                $configService->reuseOrCloneEvent(
                    blueprint: $blueprint,
                    sourceEvent: $sourceEvent,
                    cloneForm: $cloneForm,
                    customName: null,
                    customCode: null,
                    actor: $actor
                );
            }
        }
    }
}
