<?php namespace Ultimed\Requests;

class Me extends ApiRequest
{
    use IncludesOAuthAccessToken;

    public function __construct()
    {
        parent::__construct('GET', '/v1/me');
    }
}
