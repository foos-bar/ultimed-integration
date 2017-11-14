<?php namespace Ultimed\Responses;

class FileUpdate extends ApiResponse
{
    private $file;

    protected function init()
    {
        $data = $this->parseJson();
        $this->file = $data['file'];
    }

    public function getFile()
    {
        return $this->file;
    }
}
