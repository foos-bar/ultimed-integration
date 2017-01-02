<?php namespace Ultimed\Requests;

use GuzzleHttp\Psr7\MultipartStream;

class FileUpload extends ApiRequest
{
    use IncludesOAuthAccessToken;

    public function __construct($pathOrContents, $originalFilename = null)
    {
        if (strpos($pathOrContents, "\0") === false && file_exists($pathOrContents) && is_file($pathOrContents)) {
            $contents = fopen($pathOrContents, 'r');
        }
        else {
            $contents = $pathOrContents;
        }

        $file = [
            'name'     => 'file',
            'contents' => $contents,
        ];

        if ($originalFilename) {
            $file['filename'] = $originalFilename;
        }

        $body = new MultipartStream([$file]);
        parent::__construct('POST', '/v1/files', [], $body);
    }
}
