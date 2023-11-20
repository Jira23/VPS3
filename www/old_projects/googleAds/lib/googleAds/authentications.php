<?php

require  '/var/www/core/google-ads-php/vendor/autoload.php';

use Google\Auth\CredentialsLoader;
use Google\Auth\OAuth2;

class AuthenticateStandalone
{

    const SCOPE = 'https://www.googleapis.com/auth/adwords';
    const AUTHORIZATION_URI = 'https://accounts.google.com/o/oauth2/v2/auth';
    const REDIRECT_URI = 'urn:ietf:wg:oauth:2.0:oob';

    public static function getURL($clientId, $clientSecret, $stdin = '')
    {

        $scopes = self::SCOPE . ' ' . trim($stdin);

        $oauth2 = new OAuth2(
            [
                'authorizationUri' => self::AUTHORIZATION_URI,
                'redirectUri' => self::REDIRECT_URI,
                'tokenCredentialUri' => CredentialsLoader::TOKEN_CREDENTIAL_URI,
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
                'scope' => $scopes
            ]
        );

        $URL = $oauth2->buildFullAuthorizationUri();
        echo 'Log into the Google account you use for Google Ads and visit the following ';
        echo '<a href = "' .htmlspecialchars($URL) .'" target="_blank">URL</a><br><br>' ;

        return($oauth2);
    }
    
    public static function getRefreshToken($oauth2, $code){
        $code = trim($code);
        $oauth2->setCode($code);
        $authToken = $oauth2->fetchAuthToken();
        print "Your refresh token is: {$authToken['refresh_token']}" . PHP_EOL . PHP_EOL;
        return ($authToken['refresh_token']);
    }
}
