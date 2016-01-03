<?php

$client = require_once __DIR__ . '/setup-client.php';

use Ultimed\Requests;

$file = require __DIR__ . '/upload-file.php';

try {
    echo "\n\n\n\nCreate attachment ...\n";
    $patientId = 1;
    $fileId = $file['id'];
    $attachmentRequest = new Requests\AttachmentCreate($patientId, $fileId);
    $attachmentResponse = $client->send($attachmentRequest);

    var_dump($attachmentResponse->isSuccessful()); // Request successful?

    if ($attachmentResponse->isSuccessful()) {
        $attachment = $attachmentResponse->getAttachment();
        var_dump($attachment['id']); // Attachment id
        var_dump($attachment['patient']); // Patient id
        var_dump($attachment['file']); // File
        var_dump(array_keys($attachment['file'])); // array with all available properties

        return $attachment;
    }
}
catch(\GuzzleHttp\Exception\ClientException $exception) {
    echo $exception->getMessage(); exit;
}
