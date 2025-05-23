<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
     *
     * Forces HTTPS in demo environment.
     */
    public function boot(): void
    {
        if (config('app.env') === 'demo' || env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }
    }
}
