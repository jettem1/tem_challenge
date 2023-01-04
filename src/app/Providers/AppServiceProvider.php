<?php

namespace App\Providers;

use App\Services\AsyncEmail\AsyncEmailService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //Register AsyncEmail service
        $this->app->singleton(AsyncEmailService::class, function () {
            return new AsyncEmailService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
