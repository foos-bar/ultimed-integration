<?php namespace Ultimed\Responses;

class AttachmentCreate extends ApiResponse
{
    private $attachment;

    protected function init()
    {
        $data = $this->parseJson();
        $this->attachment = $data['attachment'];
    }

    public function getAttachment()
    {
        return $this->attachment;
    }
}
