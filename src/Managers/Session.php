<?php

namespace CartBoss\Api\Managers;

use CartBoss\Api\Utils;
use Delight\Cookie\Cookie;
use RandomLib\Factory;
use SecurityLib\Strength;

class Session
{
    const COOKIE_NAME = "cartboss_session";
    const QUERY_VAR = "cb_session_token";

    /**
     * @var Cookie
     */
    private $cookie;

    public function __construct()
    {
        $this->cookie = new Cookie(self::COOKIE_NAME);
        $this->cookie->setSameSiteRestriction(null);
        $this->cookie->setMaxAge(60 * 60 * 24 * 365);

        $token = Utils::get_array_value($_GET, self::QUERY_VAR, null);

        if (!self::isValidToken($token)) {
            $token = $this->cookie->get(self::COOKIE_NAME);
        }

        if (!self::isValidToken($token)) {
            $factory = new Factory;
            $generator = $factory->getGenerator(new Strength(Strength::MEDIUM));
            $token = $generator->generateString(32, "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ");
        }

        $this->cookie->setValue($token);
        $this->cookie->saveAndSet();
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->cookie->get(self::COOKIE_NAME, null);
    }

    public function reset()
    {
        $this->cookie->deleteAndUnset();
    }

    /**
     * @param mixed $token
     * @return bool
     */
    private static function isValidToken($token): bool
    {
        return !is_null($token) && 1 === preg_match("/^[a-zA-Z0-9]+$/i", $token);
    }

}
