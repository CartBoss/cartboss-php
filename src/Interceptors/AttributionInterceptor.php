<?php

namespace CartBoss\Api\Interceptors;

use CartBoss\Api\Utils;

class AttributionInterceptor
{
    const QUERY_VAR = "cb__att";

    /**
     * @var string|null
     */
    private $token;

    public function __construct()
    {
        $token = Utils::get_array_value($_GET, self::QUERY_VAR, null);
        if (isset($token)) {
            $this->token = trim($token);
        }
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }
}
