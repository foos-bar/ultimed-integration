<?php namespace Ultimed\Requests;

class Authentication extends ApiRequest
{
    use IncludesOAuthClientCredentials;

    public function __construct($username, $password)
    {
        $headers = [
            'Content-Type' => 'application/json',
        ];
        $body = json_encode([
            'grant_type' => 'integration',
            'username' => $username,
            'password' => $password,
        ]);

        parent::__construct('POST', '/oauth/access_token', $headers, $body);
    }
}
