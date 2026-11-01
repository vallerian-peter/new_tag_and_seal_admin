<?php

namespace App\Filament\Resources\LookupSets\Pages;

use App\Filament\Resources\LookupSets\LookupSetResource;
use App\Services\DynamicConfigurationService;
use App\Services\LegacyReferenceSync;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditLookupSet extends EditRecord
{
    protected static string $resource = LookupSetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('publish')
                ->color('success')->requiresConfirmation()
                ->visible(fn () => $this->record->legacy_table === null && $this->record->status === 'draft' && (auth()->user()?->hasPlatformPermission('configuration.publish') ?? false))
                ->action(function (): void {
                    $this->save(shouldRedirect: false, shouldSendSavedNotification: false);
                    app(DynamicConfigurationService::class)->publishLookup($this->record, auth()->user());
                    Notification::make()->title('Lookup set published')->success()->send();
                    $this->redirect(LookupSetResource::getUrl('index'));
                }),
        ];
    }

    protected function afterSave(): void
    {
        if ($this->record->legacy_table !== null) {
            app(LegacyReferenceSync::class)->push($this->record, auth()->user());
        }
    }
}
