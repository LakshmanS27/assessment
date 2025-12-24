<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use App\Listeners\MicrosoftSocialiteExtend;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register Microsoft Socialite provider
        $this->app['events']->listen(
            SocialiteWasCalled::class,
            MicrosoftSocialiteExtend::class
        );
    }
}
