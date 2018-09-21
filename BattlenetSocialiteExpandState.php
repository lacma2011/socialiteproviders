<?php

namespace Lacma2011\SocialiteProviders\BattlenetState;

use SocialiteProviders\Manager\SocialiteWasCalled;

class BattlenetSocialiteExpandState
{
    /**
     * Register the provider.
     *
     * @param \SocialiteProviders\Manager\SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite(
            'battlenet-stateful', __NAMESPACE__.'\Provider'
        );
    }
}
