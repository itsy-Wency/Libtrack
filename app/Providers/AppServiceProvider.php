<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /** Use Laravel's automatic container resolution for application services. */
    public function register(): void
    {
        // No manual bindings are needed for the current application services.
    }
}
