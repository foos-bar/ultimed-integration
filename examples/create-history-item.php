<?php

$client = require_once __DIR__ . '/setup-client.php';

use Carbon\Carbon;
use Ultimed\Requests;

$file = require __DIR__ . '/upload-file.php';

try {
    echo "\n\n\n\nCreate history item ...\n";
    $historyItemRequest = new Requests\HistoryItemCreate([
        'patient' => 1,
        'date' => Carbon::now(),
        'text' => 'Dekurs für Hans Wurst',
        'duration' => 30, // In Minutes
        'type' => 'dekurs',
        'services' => [1, 2],
        'people' => [1, 2],
        'files' => [$file['id']],
    ]);
    $historyItemResponse = $client->send($historyItemRequest);

    var_dump($historyItemResponse->isSuccessful()); // Request successful?

    if ($historyItemResponse->isSuccessful()) {
        $historyItem = $historyItemResponse->getHistoryItem();
        var_dump($historyItem['id']); // History item id
        var_dump($historyItem['patient']); // Patient id
        var_dump($historyItem['date']); // Date
        var_dump($historyItem['text']); // Text
        var_dump($historyItem['duration']); // Duration
        var_dump($historyItem['type']); // Type
        var_dump($historyItem['services']); // Services
        var_dump($historyItem['people']); // User
        var_dump($historyItem['files']); // Files

        return $historyItem;
    }
}
catch(\GuzzleHttp\Exception\ClientException $exception) {
    echo $exception->getMessage(); exit;
}
