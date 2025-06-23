<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force HTTPS when proxy says it's HTTPS
        if (request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
            request()->server->set('HTTPS', 'on');
            request()->server->set('SERVER_PORT', 443);
        }
    }
}
