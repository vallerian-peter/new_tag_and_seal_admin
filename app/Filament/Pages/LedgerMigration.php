<?php

namespace App\Filament\Pages;

use App\Services\FamilyAuthorityService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use UnitEnum;

class LedgerMigration extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static UnitEnum|string|null $navigationGroup = 'System & Security';

    protected static ?string $navigationLabel = 'Ledger Migration';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'ledger-migration';

    protected string $view = 'filament.pages.ledger-migration';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPlatformPermission('upgrades.view') ?? false;
    }

    public function getTitle(): string
    {
        return 'Ledger Migration';
    }

    public function canRun(): bool
    {
        return auth()->user()?->hasPlatformPermission('upgrades.run') ?? false;
    }

    public function changeAuthorityAction(): Action
    {
        return Action::make('changeAuthority')
            ->label('Change')
            ->visible(fn () => $this->canRun())
            ->modalHeading(fn (array $arguments) => 'Change '.str($arguments['family'] ?? '')->headline().' authority')
            ->modalDescription('The mobile app switches screens after its next configuration sync.')
            ->schema(fn (array $arguments) => [
                Select::make('authority')->required()->options(collect(app(FamilyAuthorityService::class)->nextStates($arguments['current'] ?? 'legacy'))
                    ->mapWithKeys(fn ($state) => [$state => FamilyAuthorityService::STATES[$state]])->all())
                    ->helperText(fn () => ($arguments['current'] ?? null) === 'ledger'
                        ? 'Retiring makes this family\'s legacy tables read-only and stops copying new events into them. Archive them afterwards with: '.FamilyAuthorityService::ARCHIVE_COMMAND.' '.($arguments['family'] ?? '')
                        : null)
                    ->rules([fn () => function (string $attribute, mixed $value, \Closure $fail) use ($arguments): void {
                        $service = app(FamilyAuthorityService::class);
                        $blocker = match (true) {
                            $value === 'ledger' && ($arguments['current'] ?? null) !== 'retired' => $service->ledgerBlocker($arguments['family'] ?? ''),
                            $value === 'retired' => $service->ledgerBlocker($arguments['family'] ?? '') ?? $service->retireBlocker(),
                            default => null,
                        };
                        if ($blocker) {
                            $fail($blocker);
                        }
                    }]),
                Textarea::make('reason')->required()->minLength(3)->maxLength(1000),
            ])
            ->action(function (array $data, array $arguments): void {
                abort_unless($this->canRun(), 403);
                app(FamilyAuthorityService::class)->change($arguments['family'], $data['authority'], auth()->user(), $data['reason']);
                Notification::make()->title('Authority changed and configuration published')->success()->send();
            });
    }

    public function appVersionAction(): Action
    {
        return Action::make('appVersion')
            ->label('Change minimum app version')
            ->visible(fn () => $this->canRun())
            ->modalDescription('When enforced, older apps cannot sign in or register and are asked to update. Signed-in devices keep working until they sign out.')
            ->fillForm(fn () => app(FamilyAuthorityService::class)->appVersion())
            ->schema([
                TextInput::make('version')->label('Minimum version')->required()->regex('/^\d+\.\d+\.\d+$/')->placeholder('1.2.0'),
                Toggle::make('enforced')->label('Refuse older apps at sign-in'),
                Textarea::make('reason')->required()->minLength(3)->maxLength(1000),
            ])
            ->action(function (array $data): void {
                abort_unless($this->canRun(), 403);
                app(FamilyAuthorityService::class)->setAppVersion($data['version'], (bool) $data['enforced'], auth()->user(), $data['reason']);
                Notification::make()->title('Minimum app version saved and configuration published')->success()->send();
            });
    }

    protected function getViewData(): array
    {
        $service = app(FamilyAuthorityService::class);

        return ['families' => $service->overview(), 'states' => FamilyAuthorityService::STATES, 'appVersion' => $service->appVersion()];
    }
}
