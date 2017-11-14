<?php

/***********************************************************
 * When user logs in                                       *
 ***********************************************************/

require __DIR__ . '/../vendor/autoload.php';

$client = new Ultimed\ApiClient([
    'base_uri' => 'http://api.ultimed.app',
    'client_id' => 2,
    'client_secret' => 'foo',
]);

$username = ''; // REPLACE: comes from user
$password = ''; // REPLACE: comes from user

$authRequest = new Ultimed\Requests\Authentication($username, $password);
$authResponse = $client->send($authRequest);
$accessToken = $authResponse->getAccessToken();

$tokenData = $accessToken->toArray();
// array like:
// [
//     'access_token' => 'abc-123-foo',
//     'token_type' => 'Bearer',
//     'expires_at' => 2015-05-05 00:00:00,
// ]

// REPLACE: Persist tokenData to database

/***********************************************************
 * Upload picture                                          *
 ***********************************************************/

require __DIR__ . '/../vendor/autoload.php';

$client = new Ultimed\ApiClient([
    'base_uri' => 'http://api.ultimed.app',
    'client_id' => 2,
    'client_secret' => 'foo',
]);

$tokenData = []; // Get tokenData from database
$accessToken = new Ultimed\OAuth\AccessToken($tokenData);
$client->setAccessToken($accessToken);

$path = 'foo.png'; // REPLACE: The picture to upload

// Upload file
$fileRequest = new Ultimed\Requests\FileUpload($path);
$fileResponse = $client->send($fileRequest);
$file = $fileResponse->getFile();

// Create connection to patient
$patientId = 1; // REPLACE: The patient the picture gets associated with
$fileUpdateRequest = new Requests\FileUpdate($file['id'], ['patient' => $patientId]);
$fileUpdateResponse = $client->send($fileUpdateRequest);
$file = $fileUpdateResponse->getFile();
