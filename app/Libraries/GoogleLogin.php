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
        $this->client->setRedirectUri(env('google.redirect_uri'));
        $this->client->addScope('email');
        $this->client->addScope('profile');
        
        log_message('error', 'GOOGLE INIT: client_id=' . env('google.client_id') . ', redirect_uri=' . env('google.redirect_uri'));
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
