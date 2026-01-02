<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use App\Models\Setting;

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
        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        // Share settings with all views
        try {
            View::composer('*', function ($view) {
                $view->with('appSettings', [
                    'company_name' => setting('company_name', config('app.name')),
                    'company_logo' => setting('company_logo'),
                    'primary_color' => setting('primary_color', '#696cff'),
                    'secondary_color' => setting('secondary_color', '#8592a3'),
                    'sidebar_text_color' => setting('sidebar_text_color', '#697a8d'),
                    'sidebar_active_bg_color' => setting('sidebar_active_bg_color', '#696cff'),
                    'sidebar_active_text_color' => setting('sidebar_active_text_color', '#ffffff'),
                ]);
            });
        } catch (\Exception $e) {
            // If settings table doesn't exist yet (during migration), skip
        }
    }
}
