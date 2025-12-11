<?php

namespace App\Providers\Filament;

use App\Filament\Auth\CustomLogin;
use App\Filament\Widgets\CustomAccountWidget;
use App\Filament\Widgets\LivestockByTypeChart;
use App\Filament\Widgets\LivestockDataStats;
use App\Filament\Widgets\PeopleUsersStats;
use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse;
use Filament\Facades\Filament;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('Tag & Seal')
            ->login(CustomLogin::class)
            ->colors([
                'primary' => Color::Green,
                'secondary' => Color::Gray,
                'tertiary' => Color::Indigo,
                'success' => Color::Emerald,
                'warning' => Color::Yellow,
                'danger' => Color::Red,
                'info' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                CustomAccountWidget::class,
                PeopleUsersStats::class,
                LivestockDataStats::class,
                LivestockByTypeChart::class,
            ])
            ->navigationGroups([
                NavigationGroup::make('Geographical')
                    ->collapsed(false),
                NavigationGroup::make('People & Users')
                    ->collapsed(false),
                NavigationGroup::make('Logs & Events')
                    ->collapsed(false),
                NavigationGroup::make('Logs Reference Data')
                    ->collapsed(false),
                NavigationGroup::make('System & Configuration')
                    ->collapsed(false),
            ])
            ->userMenuItems([
                'logout' => function (Action $action) {
                    return $action
                        ->label('Log out')
                        ->url(null) // Ensure no URL is set
                        ->modalHeading('Confirm Logout')
                        ->modalWidth('md')
                        ->modalSubmitAction(
                            Action::make('submit')
                                ->label('Yes, log out')
                                ->color('danger')
                        )
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
                },
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
