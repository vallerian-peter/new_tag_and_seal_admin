<?php

namespace App\Filament\Widgets;

use App\Models\Farmer;
use App\Models\SystemUser;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PeopleUsersStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::count())
                ->description('All user accounts in the system')
                ->descriptionIcon('heroicon-o-users')
                ->color('success')
                ->url(route('filament.admin.resources.users.index'))
                ->icon('heroicon-o-users'),
                
            Stat::make('Farmers', Farmer::count())
                ->description('Registered farmer profiles')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('info')
                ->url(route('filament.admin.resources.farmers.index'))
                ->icon('heroicon-o-user-group'),
                
            Stat::make('System Users', SystemUser::count())
                ->description('Admin and staff profiles')
                ->descriptionIcon('heroicon-o-shield-check')
                ->color('warning')
                ->url(route('filament.admin.resources.system-users.index'))
                ->icon('heroicon-o-shield-check'),
        ];
    }
}

