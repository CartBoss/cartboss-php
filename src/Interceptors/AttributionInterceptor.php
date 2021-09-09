<?php

namespace CartBoss\Api\Interceptors;

use CartBoss\Api\Utils;
use Delight\Cookie\Cookie;

class AttributionInterceptor
{
    const COOKIE_NAME = "cartboss_attribution_token";
    const COOKIE_TTL = 60 * 60 * 24 * 365;
    const QUERY_VAR = "cb__att";

    /**
     * @var Cookie
     */
    private $cookie;

    public function __construct($source = null)
    {
        $cookie = new Cookie(self::COOKIE_NAME);
        $cookie->setMaxAge(self::COOKIE_TTL);
        $cookie->setSameSiteRestriction(null);
        $this->cookie = $cookie;

        $token = Utils::get_array_value($source ?? $_GET, self::QUERY_VAR, null);
        if ($token) {
            $this->setToken($token);
        }
    }

    /**
     * @param string|null $token
     */
    private function setToken(?string $token): void
    {
        if (is_string($token) && strlen($token) > 0) {
            $this->cookie->setValue($token);
            $this->cookie->saveAndSet();
        } else {
            $this->clearCookieStorage();
        }
    }

    public function clearCookieStorage()
    {
        $this->cookie->deleteAndUnset();
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->cookie::get(self::COOKIE_NAME, null);
    }
}
