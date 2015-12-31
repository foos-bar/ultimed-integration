<?php namespace Ultimed\Requests;

class PatientShow extends ApiRequest
{
    use IncludesOAuthAccessToken;

    public function __construct($id)
    {
        parent::__construct('GET', "/v1/patients/$id");
    }
}
