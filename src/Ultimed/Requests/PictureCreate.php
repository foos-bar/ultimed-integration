<?php namespace Ultimed\Requests;

use DateTime;
use Ultimed\Presenters\EmberDate;

class PictureCreate extends ApiRequest
{
    use IncludesOAuthAccessToken;

    public function __construct($patientId, $fileId, DateTime $date)
    {
        $body = json_encode([
            'picture' => [
                'file' => ['id' => $fileId],
                'patient' => $patientId,
                'date' => (new EmberDate($date))->format(),
            ],
        ]);
        parent::__construct('POST', 'v1/pictures', [], $body);
    }
}
