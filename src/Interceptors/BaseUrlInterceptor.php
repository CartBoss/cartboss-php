<?php

namespace CartBoss\Api\Interceptors;

use CartBoss\Api\Utils;
use Delight\Cookie\Cookie;

abstract class BaseUrlInterceptor
{
    const QUERY_VAR = null;
    const COOKIE_NAME = null;
    const COOKIE_MAX_AGE = 0;

    private $namespace;

    public function __construct($namespace = null)
    {
        $this->namespace = $namespace;
        if (isset($this->namespace)) {
            $this->namespace = md5($this->namespace);
        }

        $query_val = Utils::get_array_value($_GET, static::QUERY_VAR, null);
        if (isset($query_val, $query_val)) {
            $cookie = new Cookie($this->getCookieName());

            $cookie->setHttpOnly(true);
            $cookie->setSecureOnly(false);
            $cookie->setSameSiteRestriction(null);

            $cookie->setMaxAge(static::COOKIE_MAX_AGE);
            $cookie->setValue(trim($query_val));
            $cookie->saveAndSet();
        }
    }

    protected function getValue($default = null)
    {
        return Cookie::get($this->getCookieName(), $default);
    }

    public function hasValue(): bool
    {
        return Cookie::exists($this->getCookieName());
    }

    private function getCookieName(): string
    {
        return $this->namespace ? static::COOKIE_NAME . '_' . $this->namespace : static::COOKIE_NAME;
    }
}