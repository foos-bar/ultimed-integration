<?php namespace Ultimed\Requests;

use GuzzleHttp\Psr7;
use Psr\Http\Message\MessageInterface;
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
            $headers['Authorization'] = [(string)$this->accessToken];
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

    public function withoutHeader($name): MessageInterface
    {
        $request = parent::withoutHeader($name);

        if (strtolower($name) === 'authorization' && $this->accessToken !== null) {
            // The parent cannot see the virtual token header and may return this instance.
            if ($request === $this) {
                $request = clone $this;
            }
            $request->accessToken = null;
        }

        return $request;
    }

    protected function hasValidAccessToken()
    {
        return $this->accessToken && $this->accessToken->isValid();
    }
}
