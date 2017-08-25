<?php namespace Ultimed\Responses;

class Me extends ApiResponse
{
    private $user;

    protected function init()
    {
        $data = $this->parseJson();
        $this->user = $data['user'];
    }

    public function getUser()
    {
        return $this->user;
    }
}
