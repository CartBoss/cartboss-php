<?php

namespace CartBoss\Api\Managers;

use CartBoss\Api\Storage\CookieStorage;
use CartBoss\Api\Utils;

class Session
{
    const COOKIE_NAME = "order_nonce";
    const QUERY_VAR = "cb_order_nonce";

    public function __construct()
    {
        $token = Utils::get_array_value($_GET, self::QUERY_VAR, null);

        if (!self::isValidToken($token)) {
            $token = CookieStorage::get(self::COOKIE_NAME, null);
        }

        if (!self::isValidToken($token)) {
            $token = Utils::get_random_string(64);
        }

        CookieStorage::set(self::COOKIE_NAME, $token, 60 * 60 * 24 * 365);
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return CookieStorage::get(self::COOKIE_NAME, null);
    }

    public function reset()
    {
        CookieStorage::delete(self::COOKIE_NAME);
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
