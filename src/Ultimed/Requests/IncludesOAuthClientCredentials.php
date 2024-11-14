<?php namespace Ultimed\Requests;

use GuzzleHttp\Psr7;
use Psr\Http\Message\StreamInterface;
use Ultimed\OAuth\ClientCredentials;

trait IncludesOAuthClientCredentials
{
    protected $credentials;
    protected $bodyStream;

    public function setCredentials(ClientCredentials $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * override for classes that implement Psr\Http\Message\ResponseInterface
     *
     * @see Psr\Http\Message\ResponseInterface
     * @see GuzzleHttp\Psr7\MessageTrait::getBody()
     */
    public function getBody(): StreamInterface
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
