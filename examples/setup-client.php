<?php

require __DIR__ . '/../vendor/autoload.php';

use Ultimed\ApiClient;

return new ApiClient([
    'base_uri' => 'http://api.ultimed.app',
    'client_id' => 2,
    'client_secret' => 'foo',
    // 'http_errors' => false, // Throw an exception for status codes {4xx,5xx}
]);
