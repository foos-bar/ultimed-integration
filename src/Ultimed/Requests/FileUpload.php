<?php namespace Ultimed\Requests;

use GuzzleHttp\Psr7\MultipartStream;

class FileUpload extends ApiRequest
{
    use IncludesOAuthAccessToken;

    public function __construct($path)
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException("File $path does not exist.");
        }
        else if (!is_file($path)) {
            throw new InvalidArgumentException("$path is not a file.");
        }

        $body = new MultipartStream([
            [
                'name'     => 'file',
                'contents' => fopen($path, 'r'),
            ],
        ]);
        parent::__construct('POST', '/v1/files', [], $body);
    }
}
