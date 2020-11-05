<?php namespace Ultimed\Requests;

class FileUpload extends ApiRequest
{
    use IncludesOAuthAccessToken, UploadsFile;

    public function __construct($pathOrContents, $originalFilename = null)
    {
        $body = $this->getFileUploadBody($pathOrContents, $originalFilename);
        parent::__construct('POST', '/v1/files', [], $body);
    }
}
