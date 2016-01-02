<?php

$client = require __DIR__ . '/setup-client.php';

use Ultimed\Requests;

try {
    echo "\n\n\n\nUpload file ...\n";

    $path = __DIR__ . '/foo.txt';
    file_put_contents($path, 'Foo Bar');

    $fileRequest = new Requests\FileUpload($path);
    $fileResponse = $client->send($fileRequest);

    unlink($path);

    var_dump($fileResponse->isSuccessful()); // Request successful?

    if ($fileResponse->isSuccessful()) {
        $file = $fileResponse->getFile();
        var_dump($file['id']); // File id
        var_dump($file['name']); // File name
        var_dump($file['url']); // File url
        var_dump($file['thumbnailUrl']); // File thumbnail_url

        return $file;
    }
}
catch(\GuzzleHttp\Exception\ClientException $exception) {
    echo $exception->getMessage(); exit;
}
