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

        if (array_key_exists('expires_at', $data)) {
            $this->expiresAt = Carbon::createFromFormat('Y-m-d H:i:s', $data['expires_at']);
        }
        else if (array_key_exists('expires_in', $data)) {
            $this->expiresAt = Carbon::now()->addSeconds($data['expires_in']);
        }

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

    public function toArray()
    {
        return [
            'access_token' => $this->getAccessToken(),
            'token_type' => $this->getTokenType(),
            'expires_at' => $this->getExpiresAt(),
            'user_id' => $this->getUserId(),
        ];
    }
}
