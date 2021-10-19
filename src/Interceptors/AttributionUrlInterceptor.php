<?php

namespace CartBoss\Api\Interceptors;

class AttributionUrlInterceptor extends BaseUrlInterceptor
{
    const QUERY_VAR = "cb__att";
    const COOKIE_NAME = "cbx_attribution";
    const COOKIE_MAX_AGE = 60 * 60 * 24 * 7;

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->getValue();
    }
}