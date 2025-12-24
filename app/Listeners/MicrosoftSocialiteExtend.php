<?php

namespace App\Listeners;

use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Microsoft\Provider;

class MicrosoftSocialiteExtend
{
    public function handle(SocialiteWasCalled $event): void
    {
        $event->extendSocialite('microsoft', Provider::class);
    }
}
