<?php

namespace CartBoss\Api\Interceptors;

use CartBoss\Api\Resources\Attribution;
use CartBoss\Api\Utils;

class AttributionInterceptor
{
    const QUERY_VAR = "cb__att";

    /**
     * @var Attribution
     */
    private $token;

    public function __construct()
    {
        $this->token = new Attribution(Utils::getArrayValue($_GET, static::QUERY_VAR, ''));
    }

    /**
     * @return Attribution
     */
    public function getAttribution(): Attribution
    {
        return $this->token;
    }
}