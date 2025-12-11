<?php

namespace App\Filament\Widgets;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse;
use Filament\Facades\Filament;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Widgets\AccountWidget as BaseAccountWidget;

class CustomAccountWidget extends BaseAccountWidget implements HasActions
{
    use InteractsWithActions;

    /**
     * @var view-string
     */
    protected string $view = 'filament.widgets.custom-account-widget';

    protected function getActions(): array
    {
        return [
            $this->getLogoutAction(),
        ];
    }

    public function getLogoutAction(): Action
    {
        return Action::make('logout')
            ->label(__('filament-panels::widgets/account-widget.actions.logout.label'))
            ->icon(\Filament\Support\Icons\Heroicon::ArrowLeftEndOnRectangle)
            ->color('gray')
            ->size('lg')
            ->url(null) // Ensure no URL is set
            ->modalHeading('Confirm Logout')
            ->modalWidth('md')
            ->modalSubmitActionLabel('Yes, log out')
            ->modalCancelActionLabel('Cancel')
            ->infolist([
                Section::make()
                    ->schema([
                        TextEntry::make('message')
                            ->label('')
                            ->default('Are you sure you want to logout from your account?')
                            ->weight(FontWeight::Medium)
                            ->columnSpanFull(),
                        TextEntry::make('warning')
                            ->label('')
                            ->default('You will need to login again to access the admin panel.')
                            ->color('gray')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ])
            ->action(function (): LogoutResponse {
                Filament::auth()->logout();
                session()->invalidate();
                session()->regenerateToken();

                return app(LogoutResponse::class);
            });
    }
}

