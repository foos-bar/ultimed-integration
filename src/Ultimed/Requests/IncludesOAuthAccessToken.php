<?php namespace Ultimed\Requests;

use GuzzleHttp\Psr7;
use Ultimed\OAuth\AccessToken;

trait IncludesOAuthAccessToken
{
    protected $accessToken;

    public function setAccessToken(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function getHeaders(): array
    {
        $headers = parent::getHeaders();
        if ($this->hasValidAccessToken()) {
            $headers['Authorization'] = (string)$this->accessToken;
        }

        return $headers;
    }

    public function hasHeader($name): bool
    {
        if (strtolower($name) === 'authorization') {
            return $this->hasValidAccessToken();
        }

        return parent::hasHeader($name);
    }

    public function getHeader($name): array
    {
        $headers = parent::getHeader($name);

        if (strtolower($name) === 'authorization' &&
            $this->hasValidAccessToken()) {
            $headers[] = (string)$this->accessToken;
        }

        return $headers;
    }

    protected function hasValidAccessToken()
    {
        return $this->accessToken && $this->accessToken->isValid();
    }
}
