<?php

$client = require __DIR__ . '/setup-client.php';

use Ultimed\Requests;

// Login
$accessToken = require __DIR__ . '/authenticate.php';
$client->setAccessToken($accessToken);

try {
    echo "\n\n\n\nQuery for patient details ...\n";
    $patientRequest = new Requests\PatientShow(1);
    $patientResponse = $client->send($patientRequest);

    var_dump($patientResponse->isSuccessful()); // Request successful?

    if ($patientResponse->isSuccessful()) {
        $patient = $patientResponse->getPatient();
        var_dump($patient['firstName']); // First name
        var_dump($patient['lastName']); // Last name
        var_dump($patient['categories']); // Relationship
        var_dump(array_keys($patient)); // array with all available properties
    }
}
catch(\GuzzleHttp\Exception\ClientException $exception) {
    echo $exception->getMessage(); exit;
}
