<?php namespace Ultimed\Responses;

use GuzzleHttp\Psr7\Response;
use Ultimed\OAuth\AccessToken;

class Authentication extends ApiResponse
{
    private $accessToken;

    protected function init()
    {
        $this->accessToken = new AccessToken($this->parseJson());
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }
}
