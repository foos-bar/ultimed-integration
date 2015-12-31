<?php namespace Ultimed;

use Exception;
use GuzzleHttp\Client;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Ultimed\OAuth\ClientCredentials;
use Ultimed\OAuth\AccessToken;
use Ultimed\Requests\ApiRequest;
use Ultimed\Requests\IncludesOAuthClientCredentials;
use Ultimed\Requests\IncludesOAuthAccessToken;
use Ultimed\Requests\Authentication as AuthenticationRequest;
use Ultimed\Responses\Authentication as AuthenticationResponse;

class ApiClient extends Client
{
    protected $credentials;
    protected $accessToken;

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

    public function setAccessToken(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;
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

        if (in_array(IncludesOAuthAccessToken::class, $traits)) {
            $request->setAccessToken($this->accessToken);
        }

        return $request;
    }

    private function convertResponse(
        RequestInterface $request,
        ResponseInterface $response)
    {
        $response = $this->wrapResponseInCustomClass($request, $response);

        if ($request instanceof AuthenticationRequest) {
            $this->accessToken = $response->getAccessToken();
        }

        return $response;
    }

    private function wrapResponseInCustomClass(
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
