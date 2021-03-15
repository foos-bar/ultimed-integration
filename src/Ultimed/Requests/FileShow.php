<?php namespace Ultimed\Requests;

class FileShow extends ApiRequest
{
    use IncludesOAuthAccessToken;

    public function __construct($id)
    {
        parent::__construct('GET', "/v1/files/$id");
    }
}
