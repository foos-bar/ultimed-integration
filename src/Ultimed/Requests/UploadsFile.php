<?php namespace Ultimed\Requests;

use GuzzleHttp\Psr7\MultipartStream;

trait UploadsFile
{
    private function getFileUploadBody($pathOrContents, $originalFilename = null)
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

        return new MultipartStream([$file]);
    }
}
