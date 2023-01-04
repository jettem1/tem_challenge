<?php

namespace App\Providers;

use App\Services\AsyncEmail\AsyncEmailService;
use App\Services\AsyncEmail\Transport\MailJetEmailTransport;
use App\Services\AsyncEmail\Transport\SendGridEmailTransport;
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

            $asyncEmailService = new AsyncEmailService();

            $asyncEmailService->addEmailTransport((new MailJetEmailTransport(
                config('asyncemail.mailjet.key'), config('asyncemail.mailjet.secret'), config('asyncemail.mailjet.senderEmail'))));

            $asyncEmailService->addEmailTransport((new SendGridEmailTransport(
                config('asyncemail.sendgrid.apiKey'), config('asyncemail.mailjet.senderEmail'))));


            return $asyncEmailService;
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
