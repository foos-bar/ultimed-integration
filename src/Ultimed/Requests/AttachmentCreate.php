<?php namespace Ultimed\Requests;

class AttachmentCreate extends ApiRequest
{
    use IncludesOAuthAccessToken;

    public function __construct($patientId, $fileId)
    {
        $body = json_encode([
            'attachment' => [
                'file' => $fileId,
                'patient' => $patientId,
            ],
        ]);
        parent::__construct('POST', 'v1/attachments', [], $body);
    }
}
