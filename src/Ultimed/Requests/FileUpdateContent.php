<?php namespace Ultimed\Requests;

class FileUpdateContent extends ApiRequest
{
    use IncludesOAuthAccessToken, UploadsFile;

    public function __construct($pathOrContents, $id)
    {
        $body = $this->getFileUploadBody($pathOrContents);
        parent::__construct('POST', "/v1/files/$id/content", [], $body);
    }
}
