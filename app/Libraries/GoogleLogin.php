<?php

namespace App\Libraries;

use Google_Client;

class GoogleLogin
{
    protected $client;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setClientId(env('google.client_id'));
        $this->client->setClientSecret(env('google.client_secret'));

        helper('url');
        $redirectUri = base_url('auth/google-callback');
        $this->client->setRedirectUri($redirectUri);

        $this->client->addScope('email');
        $this->client->addScope('profile');
        
        log_message('error', 'GOOGLE INIT: client_id=' . env('google.client_id') . ', redirect_uri=' . $redirectUri);
    }

    public function setState($state)
    {
        $this->client->setState($state);
    }

    public function getAuthUrl()
    {
        $this->client->setPrompt('select_account');
        $redirectUri = $this->client->getRedirectUri();
        log_message('error', 'GOOGLE AUTH: Redirect URI = ' . $redirectUri);
        return $this->client->createAuthUrl();
    }

    public function getAccessToken($code)
    {
        return $this->client->fetchAccessTokenWithAuthCode($code);
    }

    public function getUserInfo($accessToken)
    {
        $this->client->setAccessToken($accessToken);
        $oauth2 = new \Google_Service_Oauth2($this->client);
        return $oauth2->userinfo->get();
    }
}
