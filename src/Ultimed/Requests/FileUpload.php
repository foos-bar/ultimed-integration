<?php namespace Ultimed\Requests;

use GuzzleHttp\Psr7\MultipartStream;

class FileUpload extends ApiRequest
{
    use IncludesOAuthAccessToken;

    public function __construct($path, $originalFilename = null)
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException("File $path does not exist.");
        }
        else if (!is_file($path)) {
            throw new InvalidArgumentException("$path is not a file.");
        }

        $file = [
            'name'     => 'file',
            'contents' => fopen($path, 'r'),
        ];

        if ($originalFilename) {
            $file['filename'] = $originalFilename;
        }

        $body = new MultipartStream([$file]);
        parent::__construct('POST', '/v1/files', [], $body);
    }
}
