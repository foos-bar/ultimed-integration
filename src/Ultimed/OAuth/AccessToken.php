<?php namespace Ultimed\OAuth;

use Carbon\Carbon;

class AccessToken
{
    private $accessToken;
    private $tokenType;
    private $expiresAt;
    private $userId;

    public function __construct(array $data = [])
    {
        $this->accessToken = $data['access_token'];
        $this->tokenType = $data['token_type'];
        $this->expiresAt = Carbon::now()->addSeconds($data['expires_in']);
        $this->userId = $data['user_id'];
    }

    public function __toString()
    {
        return "{$this->tokenType} {$this->accessToken}";
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getTokenType()
    {
        return $this->tokenType;
    }

    public function getExpiresIn()
    {
        return Carbon::now()->diffInSeconds($this->expiresAt);
    }

    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    public function isValid()
    {
        return Carbon::now()->lte($this->getExpiresAt());
    }

    public function getUserId()
    {
        return $this->userId;
    }
}
