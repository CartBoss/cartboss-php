<?php

namespace CartBoss\Api\Storage;

use CartBoss\Api\Interfaces\StorageInterface;
use Delight\Cookie\Cookie;

class CookieStorage implements StorageInterface
{
    const NAMESPACE = '_cb_';

    public static function set($name, $value, $timeout = 60 * 60 * 24 * 7)
    {
        if (isset($name, $value, $timeout)) {
            $cookie = new Cookie(self::NAMESPACE . $name);
            $cookie->setMaxAge($timeout);
            $cookie->setHttpOnly(true);
            $cookie->setSecureOnly(false);
            $cookie->setSameSiteRestriction(null);

            $cookie->setValue(base64_encode($value));

            $cookie->saveAndSet();
        }
    }

    public static function get($name, $default = null)
    {
        $value = Cookie::get(self::NAMESPACE . $name);
        if (isset($value) && is_string($value)) {
            return base64_decode($value);
        }

        return $default;
    }

    public static function delete($name)
    {
        $cookie = new Cookie(self::NAMESPACE . $name);
        $cookie->deleteAndUnset();
    }
}