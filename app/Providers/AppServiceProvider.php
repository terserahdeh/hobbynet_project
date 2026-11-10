<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\View\Composers\OtherHobbiesComposer;
use Illuminate\Support\Facades\View;

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
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
        View::composer('partials.other-hobbies', OtherHobbiesComposer::class);
    }
}
