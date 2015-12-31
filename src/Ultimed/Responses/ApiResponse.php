<?php namespace Ultimed\Responses;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Psr7\Response;

class ApiResponse implements ResponseInterface
{
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;

        if ($this->isSuccessful()) {
            $this->init();
        }
    }

    public function isSuccessful()
    {
        $statusCode = $this->getStatusCode();
        return $statusCode >= 200 && $statusCode < 300;
    }

    public function parseJson()
    {
        $json = $this->response->getBody()->getContents();
        return json_decode($json, true);
    }

    protected function init()
    {
        // Do nothing, can be overridden by sub classes.
    }

    //
    // Delegated Methods
    //

    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        return $this->response->withStatus($code, $reasonPhrase);
    }

    public function getReasonPhrase()
    {
        return $this->response->getReasonPhrase();
    }

    public function getProtocolVersion()
    {
        return $this->response->getProtocolVersion();
    }

    public function withProtocolVersion($version)
    {
        return $this->response->withProtocolVersion($version);
    }

    public function getHeaders()
    {
        return $this->response->getHeaders();
    }

    public function hasHeader($name)
    {
        return $this->response->hasHeader($name);
    }

    public function getHeader($name)
    {
        return $this->response->getHeader($name);
    }

    public function getHeaderLine($name)
    {
        return $this->response->getHeaderLine($name);
    }

    public function withHeader($name, $value)
    {
        return $this->response->withHeader($name, $value);
    }

    public function withAddedHeader($name, $value)
    {
        return $this->response->withAddedHeader($name, $value);
    }

    public function withoutHeader($name)
    {
        return $this->response->withoutHeader($name);
    }

    public function getBody()
    {
        return $this->response->getBody();
    }

    public function withBody(StreamInterface $body)
    {
        return $this->response->withBody($body);
    }

}
