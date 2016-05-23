<?php namespace SocialNorm\Dropbox;

use SocialNorm\Exceptions\InvalidAuthorizationCodeException;
use SocialNorm\Providers\OAuth2Provider;

class DropboxProvider extends OAuth2Provider
{
    protected $authorizeUrl = "https://www.dropbox.com/1/oauth2/authorize";
    protected $accessTokenUrl = "https://api.dropbox.com/1/oauth2/token";
    protected $userDataUrl = "https://api.dropbox.com/1/account/info";
    protected $scope = [];

    protected $headers = [
        'authorize' => [],
        'access_token' => [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ],
        'user_details' => [],
    ];

    protected function compileScopes()
    {
        return implode(' ', $this->scope);
    }

    protected function getAuthorizeUrl()
    {
        return $this->authorizeUrl;
    }

    protected function getAccessTokenBaseUrl()
    {
        return $this->accessTokenUrl;
    }

    protected function getUserDataUrl()
    {
        return $this->userDataUrl;
    }

    protected function parseTokenResponse($response)
    {
        return $this->parseJsonTokenResponse($response);
    }

    protected function parseUserDataResponse($response)
    {
        return json_decode($response, true);
    }

    protected function userId()
    {
        return $this->getProviderUserData('uid');
    }

    protected function nickname()
    {
        return $this->getProviderUserData('email');
    }

    protected function fullName()
    {
        return $this->getProviderUserData('given_name') . ' ' . $this->getProviderUserData('surname');
    }

    protected function avatar()
    {
        return null; // No avatar returned
    }

    protected function email()
    {
        return $this->getProviderUserData('email');
    }
}
