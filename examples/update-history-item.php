<?php

$client = require_once __DIR__ . '/setup-client.php';

use Carbon\Carbon;
use Ultimed\Requests;

$historyItem = require __DIR__ . '/create-history-item.php';

try {
    echo "\n\n\n\nUpdate history item ...\n";
    $historyItemRequest = new Requests\HistoryItemUpdate($historyItem['id'], [
        'text' => 'Change the text',
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
        var_dump($historyItem['pictures']); // Pictures
        var_dump($historyItem['attachments']); // Attachments

        return $historyItem;
    }
}
catch(\GuzzleHttp\Exception\ClientException $exception) {
    echo $exception->getMessage(); exit;
}
