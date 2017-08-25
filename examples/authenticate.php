<?php

$shouldNotAuthenticate = true; // Turn off auto login
$client = require __DIR__ . '/setup-client.php';

use Ultimed\Requests;

try {
    echo "Authenticating user ...\n";
    $authRequest = new Requests\Authentication('felix', 'felix');
    $authResponse = $client->send($authRequest);

    var_dump($authResponse->isSuccessful()); // Auth request successful?

    if ($authResponse->isSuccessful()) {
        $accessToken = $authResponse->getAccessToken();
        var_dump($accessToken->getAccessToken()); // The actual access token
        var_dump($accessToken->getTokenType()); // The token type (e.g. Bearer)
        var_dump($accessToken->getExpiresIn()); // Seconds until it expires
        var_dump($accessToken->getExpiresAt()); // Date when it expires
        var_dump($accessToken->isValid()); // Access token still valid?
        
        $meRequest = new Requests\Me;
        $meResponse = $client->send($meRequest);
        var_dump($meResponse->getUser()); // User associated with token

        return $accessToken;
    }
}
catch(\GuzzleHttp\Exception\ClientException $exception) {
    echo $exception->getMessage(); exit;
}
