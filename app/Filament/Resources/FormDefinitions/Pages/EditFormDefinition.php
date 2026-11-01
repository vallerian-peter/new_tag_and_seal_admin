<?php

namespace App\Filament\Resources\FormDefinitions\Pages;

use App\Filament\Resources\FormDefinitions\FormDefinitionResource;
use App\Services\DynamicConfigurationService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditFormDefinition extends EditRecord
{
    protected static string $resource = FormDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('publish')
                ->color('success')
                ->visible(fn () => $this->record->status === 'draft' && auth()->user()?->hasPlatformPermission('configuration.publish'))
                ->action(function (): void {
                    $this->save(shouldRedirect: false, shouldSendSavedNotification: false);
                    app(DynamicConfigurationService::class)->publishForm($this->record, auth()->user());
                    Notification::make()->title('Form published')->success()->send();
                    $this->redirect(FormDefinitionResource::getUrl('index'));
                }),
        ];
    }
}
