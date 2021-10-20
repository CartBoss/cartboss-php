<?php

namespace CartBoss\Api\Storage;

use CartBoss\Api\Interfaces\StorageInterface;
use Delight\Cookie\Cookie;

class CookieStorage implements StorageInterface
{
    const NAMESPACE = 'cbx_';

    public static function set($name, $value, $timeout = 60 * 60 * 24 * 7)
    {
        if (isset($name, $value, $timeout)) {
            $cookie = new Cookie(self::NAMESPACE . $name);
            $cookie->setValue($value);
            $cookie->setMaxAge($timeout);
            $cookie->setHttpOnly(true);
            $cookie->setSecureOnly(false);
            $cookie->setSameSiteRestriction(null);
            $cookie->saveAndSet();
        }
    }

    public static function get($name, $default = null)
    {
        return Cookie::get(self::NAMESPACE . $name, $default);
    }

    public static function delete($name)
    {
        $cookie = new Cookie(self::NAMESPACE . $name);
        $cookie->deleteAndUnset();
    }
}