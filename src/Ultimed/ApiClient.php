<?php namespace Ultimed;

use Exception;
use GuzzleHttp\Client;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Ultimed\OAuth\ClientCredentials;
use Ultimed\Requests\ApiRequest;
use Ultimed\Requests\IncludesOAuthClientCredentials;

class ApiClient extends Client
{
    protected $credentials;

    public function __construct(array $config = [])
    {
        if (array_key_exists('client_id', $config) &&
            array_key_exists('client_secret', $config)) {
            $this->credentials = new ClientCredentials(
                $config['client_id'],
                $config['client_secret']
            );
        }
        else {
            throw new Exception('Please specify client_id and client_secret.');
        }

        parent::__construct($config);
    }

    public function send(RequestInterface $request, array $options = [])
    {
        $request = $this->prepareRequest($request);

        $response = parent::send($request, $options);
        $response = $this->convertResponse($request, $response);

        return $response;
    }

    private function prepareRequest(RequestInterface $request)
    {
        $traits = class_uses($request);

        if (in_array(IncludesOAuthClientCredentials::class, $traits)) {
            $request->setCredentials($this->credentials);
        }

        return $request;
    }

    private function convertResponse(
        RequestInterface $request,
        ResponseInterface $response)
    {
        if ($request instanceof ApiRequest) {
            $responseClass = str_replace('Request', 'Response', get_class($request));
            if (class_exists($responseClass)) {
                return new $responseClass($response);
            }
        }

        return $response;
    }
}
