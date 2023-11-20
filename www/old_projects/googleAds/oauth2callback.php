<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);


    $server_name = 'localhost';
    
// Load the Google API PHP Client Library.
require_once '/var/www/core/google-api-php-client-2.4.1/vendor/autoload.php';

// Start a session to persist credentials.
session_start();

// Create the client object and set the authorization configuration
// from the client_secrets.json you downloaded from the Developers Console.
$client = new Google_Client();
$client->setAuthConfig('/var/www/core/google-api-php-client-2.4.1/client_secrets.json');
$client->setRedirectUri('http://' . $server_name . '/oauth2callback.php');
$client->addScope(Google_Service_Analytics::ANALYTICS_READONLY);

// Handle authorization flow from the server.
if (! isset($_GET['code'])) {
  $auth_url = $client->createAuthUrl();
  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  //$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/';
  $redirect_uri = 'http://194.182.64.183/';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

//oauth2callback.php?code=4/yQHiFVAO47h0vT8IgXl91y4zmBAo9X4fawZN5Gks2VLxGEiQEd_4UiC5QCto9n_fI6D2igNr2QItURReMF--XVk&scope=https://www.googleapis.com/auth/analytics.readonly