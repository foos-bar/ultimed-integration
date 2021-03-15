<?php namespace Ultimed\Responses;

class FileUpdate extends ApiResponse
{
    protected $file;

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
