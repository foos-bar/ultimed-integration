<?php namespace Ultimed\Requests;

use GuzzleHttp\Psr7\Uri;

class PatientSearch extends ApiRequest
{
    use IncludesOAuthAccessToken;

    public function __construct($search)
    {
        $search = urlencode($search);
        $query = "search=$search";

        $uri = (new Uri('/v1/patients/search'))->withQuery($query);

        parent::__construct('GET', $uri);
    }
}
