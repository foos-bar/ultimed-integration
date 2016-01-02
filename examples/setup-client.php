<?php

require __DIR__ . '/../vendor/autoload.php';

use Ultimed\ApiClient;

if (!isset($_client)) {
    $_client = new ApiClient([
        'base_uri' => 'http://api.ultimed.app',
        'client_id' => 2,
        'client_secret' => 'foo',
        // 'http_errors' => false, // Throw an exception for status codes {4xx,5xx}
    ]);
}

if (!$_client->isAuthenticated() && empty($shouldNotAuthenticate)) {
    // Login
    $accessToken = require __DIR__ . '/authenticate.php';
    $_client->setAccessToken($accessToken);
}

return $_client;
