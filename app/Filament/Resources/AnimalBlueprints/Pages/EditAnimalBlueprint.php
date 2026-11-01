<?php

namespace App\Filament\Resources\AnimalBlueprints\Pages;

use App\Filament\Resources\AnimalBlueprints\AnimalBlueprintResource;
use App\Services\DynamicConfigurationService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAnimalBlueprint extends EditRecord
{
    protected static string $resource = AnimalBlueprintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('publish')
                ->requiresConfirmation()->color('success')
                ->visible(fn () => $this->record->status === 'draft' && auth()->user()?->hasPlatformPermission('configuration.publish'))
                ->action(function (): void {
                    $this->save(shouldRedirect: false, shouldSendSavedNotification: false);
                    app(DynamicConfigurationService::class)->publishBlueprint($this->record, auth()->user());
                    Notification::make()->title('Blueprint published and manifest compiled')->success()->send();
                    $this->redirect(AnimalBlueprintResource::getUrl('index'));
                }),
            Action::make('clone')
                ->visible(fn () => $this->record->status !== 'draft' && auth()->user()?->hasPlatformPermission('configuration.manage'))
                ->action(function (): void {
                    $clone = app(DynamicConfigurationService::class)->cloneBlueprint($this->record, auth()->user());
                    $this->redirect(AnimalBlueprintResource::getUrl('edit', ['record' => $clone]));
                }),
        ];
    }
}
