<?php namespace Ultimed\Requests;

use GuzzleHttp\Psr7\Uri;

class PatientSearchByCustomFields extends ApiRequest
{
    use IncludesOAuthAccessToken;

    public function __construct($customFields)
    {
        $uri = new Uri('/v1/patients');
        foreach ($customFields as $fieldName => $value) {
          $uri = $uri->withQuery("filter[$fieldName]=$value");
        }

        parent::__construct('GET', $uri);
    }
}
