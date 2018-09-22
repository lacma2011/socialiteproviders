<?php

namespace Lacma2011\SocialiteProviders\BattlenetState;

use SocialiteProviders\Battlenet\Provider as BattlenetProvider;
use SocialiteProviders\Manager\OAuth2\User;

/**
 *  How to use state with Socialite providers, thanks to
 *  Kaktus@Stack Overflow  https://stackoverflow.com/questions/44621555/laravel-socialite-save-url-before-redirection
 */
class Provider extends BattlenetProvider
{
    protected $stateRegion = null;

    public function withRegion($region)
    {
        $this->stateRegion = $region;

        return $this;
    }

    protected function getState()
    {
        // The state becomes a JSON object with both the XRSF protection token and the url
        return json_encode([
            'state' => parent::getState(),
            'region' => $this->stateRegion,
        ]);
    }

    protected function hasInvalidState()
    {
        if ($this->isStateless()) {
            return false;
        }

        $storedState = $this->request->session()->pull('state');
        $requestState = $this->request->input('state');
        $requestStateData = json_decode($requestState, true);

        // If the JSON is valid we extract the url here
        if (!is_null($requestStateData) && array_key_exists('region', $requestStateData)) {
            // Don't forget, this value is unsafe. Do additional checks before redirecting to that url
            $this->stateRegion = $requestStateData['region'];
        }

        // If you don't share your session between your instances you can play it "stateless" by always returning false here
        // Doing so you loose all XRSF protection ! (but this might be the only way if you don't share your cookies)
        // return false;

        // If the session is shared, we finish by checking the full state
        // We compare the full json objects, no need to extract the state parameter
        return ! (strlen($storedState) > 0 && $requestState === $storedState);
    }

    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            // Data here will vary from provider to provider.
            'id'    => $user['id'],
            'battletag' => $user['battletag'],
            // We add the extracted URL here so it can be access from the controller
            'region' => $this->stateRegion,
        ]);
    }
    
}
