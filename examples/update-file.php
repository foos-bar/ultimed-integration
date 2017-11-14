<?php

$client = require_once __DIR__ . '/setup-client.php';

use Carbon\Carbon;
use Ultimed\Requests;

$file = require __DIR__ . '/upload-file.php';

try {
    echo "\n\n\n\nUpdate file ...\n";
    $fileRequest = new Requests\FileUpdate($file['id'], [
        'patient' => 123,
    ]);
    $fileResponse = $client->send($fileRequest);

    var_dump($fileResponse->isSuccessful()); // Request successful?

    if ($fileResponse->isSuccessful()) {
        $file = $fileResponse->getFile();
        var_dump($file['id']); // File id
        var_dump($file['name']); // Name
        var_dump($file['patient']); // Patient

        return $file;
    }
}
catch(\GuzzleHttp\Exception\ClientException $exception) {
    echo $exception->getMessage(); exit;
}
