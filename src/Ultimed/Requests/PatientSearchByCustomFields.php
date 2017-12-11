<?php namespace Ultimed\Requests;

use GuzzleHttp\Psr7\Uri;

class PatientSearchByCustomFields extends ApiRequest
{
    use IncludesOAuthAccessToken;

    public function __construct($customFields)
    {
        $query = implode('&', array_map(function($fieldName, $value) {
            return "filter[$fieldName]=$value";
        }, array_keys($customFields), $customFields));

        $uri = (new Uri('/v1/patients'))->withQuery($query);

        parent::__construct('GET', $uri);
    }
}
