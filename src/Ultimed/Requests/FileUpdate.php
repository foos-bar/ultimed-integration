<?php namespace Ultimed\Requests;

class FileUpdate extends ApiRequest
{
    use IncludesOAuthAccessToken;

    public function __construct($id, array $data = [])
    {
        $body = json_encode(['file' => $data]);
        parent::__construct('PUT', "v1/files/$id", [], $body);
    }
}
