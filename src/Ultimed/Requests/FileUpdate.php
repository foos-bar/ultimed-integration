<?php namespace Ultimed\Requests;

use Ultimed\Presenters\EmberDate;

class FileUpdate extends ApiRequest
{
    use IncludesOAuthAccessToken;

    public function __construct($id, array $data = [])
    {
        if (array_key_exists('date', $data)) {
            $data['date'] = (new EmberDate($data['date']))->format();
        }
        $body = json_encode(['file' => $data]);
        parent::__construct('PUT', "v1/files/$id", [], $body);
    }
}
