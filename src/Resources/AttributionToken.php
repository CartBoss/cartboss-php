<?php

namespace CartBoss\Api\Resources;

class AttributionToken
{
    protected $token;

    public function __construct($token)
    {
        $this->token = trim($token);
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function isValid(): bool
    {
        return !empty($this->token);
    }

    public function __toString()
    {
        return $this->token;
    }
}