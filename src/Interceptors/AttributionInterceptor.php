<?php

namespace CartBoss\Api\Interceptors;

use CartBoss\Api\Resources\AttributionToken;
use CartBoss\Api\Utils;

class AttributionInterceptor
{
    const QUERY_VAR = "cb__att";

    /**
     * @var AttributionToken
     */
    private $token;

    public function __construct()
    {
        $this->token = new AttributionToken(Utils::get_array_value($_GET, static::QUERY_VAR, ''));
    }

    /**
     * @return AttributionToken
     */
    public function getToken(): AttributionToken
    {
        return $this->token;
    }
}