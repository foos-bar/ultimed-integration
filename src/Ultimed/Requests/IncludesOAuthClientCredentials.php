<?php namespace Ultimed\Requests;

use GuzzleHttp\Psr7;
use Ultimed\OAuth\ClientCredentials;

trait IncludesOAuthClientCredentials
{
    protected $credentials;
    protected $bodyStream;

    public function setCredentials(ClientCredentials $credentials)
    {
        $this->credentials = $credentials;
    }

    public function getBody()
    {
        if ($this->bodyStream !== null) {
            return $this->bodyStream;
        }

        $stream = parent::getBody();

        if ($this->credentials === null) {
            return $this->bodyStream = $stream;
        }

        $data = json_decode($stream->getContents(), true);
        $data = array_merge($this->credentials->toArray(), $data);
        $json = json_encode($data);
        return $this->bodyStream = Psr7\stream_for($json);
    }
}
