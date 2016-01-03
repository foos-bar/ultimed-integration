<?php

$client = require __DIR__ . '/setup-client.php';

use Ultimed\Requests;

try {
    echo "\n\n\n\nQuery for patients with a certain custom field ...\n";
    $patientsRequest = new Requests\PatientSearchByCustomFields([
        'phone' => urlencode('+436644204012'),
    ]);
    $patientsResponse = $client->send($patientsRequest);

    var_dump($patientsResponse->isSuccessful()); // Request successful?

    if ($patientsResponse->isSuccessful()) {
        $patients = $patientsResponse->getPatients();
        foreach ($patients as $patient) {
            var_dump($patient['firstName']); // First name
            var_dump($patient['lastName']); // Last name
            var_dump($patient['categories']); // Relationship
            var_dump(array_keys($patient)); // array with all available properties
        }

        return $patients;
    }
}
catch(\GuzzleHttp\Exception\ClientException $exception) {
    echo $exception->getMessage(); exit;
}
