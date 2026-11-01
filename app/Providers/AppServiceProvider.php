<?php

namespace App\Providers;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Destructive actions stay frozen until each resource has an explicit,
        // audited archive/retire/void workflow. Livestock uses its own archive action.
        DeleteAction::configureUsing(fn (DeleteAction $action) => $action->visible(false));
        DeleteBulkAction::configureUsing(fn (DeleteBulkAction $action) => $action->visible(false));
        ForceDeleteAction::configureUsing(fn (ForceDeleteAction $action) => $action->visible(false));
        ForceDeleteBulkAction::configureUsing(fn (ForceDeleteBulkAction $action) => $action->visible(false));
    }
}
