<?php

$client = require_once __DIR__ . '/setup-client.php';

use Carbon\Carbon;
use Ultimed\Requests;

$file = require __DIR__ . '/upload-file.php';

try {
    echo "\n\n\n\nCreate picture ...\n";
    $patientId = 1;
    $fileId = $file['id'];
    $date = Carbon::now();
    $pictureRequest = new Requests\PictureCreate($patientId, $fileId, $date);
    $pictureResponse = $client->send($pictureRequest);

    var_dump($pictureResponse->isSuccessful()); // Request successful?

    if ($pictureResponse->isSuccessful()) {
        $picture = $pictureResponse->getPicture();
        var_dump($picture['id']); // picture id
        var_dump($picture['patient']); // Patient id
        var_dump($picture['date']); // Date
        var_dump($picture['file']); // File
        var_dump(array_keys($picture['file'])); // array with all available properties
    }
}
catch(\GuzzleHttp\Exception\ClientException $exception) {
    echo $exception->getMessage(); exit;
}
