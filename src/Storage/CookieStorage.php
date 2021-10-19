<?php

namespace CartBoss\Api\Storage;

use CartBoss\Api\Interfaces\StorageInterface;
use Delight\Cookie\Cookie;

class CookieStorage implements StorageInterface
{
    const NAMESPACE = 'cbx_';

    public function set($name, $value, $max_age = 60 * 60 * 24)
    {
        if (isset($name, $value, $max_age)) {
            $cookie = new Cookie(self::NAMESPACE . $name);
            $cookie->setValue($value);
            $cookie->setMaxAge($max_age);
            $cookie->setHttpOnly(true);
            $cookie->setSecureOnly(false);
            $cookie->setSameSiteRestriction(null);
            $cookie->saveAndSet();
        }
    }

    public function get($name, $default = null)
    {
        return Cookie::get(self::NAMESPACE .$name, $default);
    }

    public function delete($name)
    {
        $cookie = new Cookie(self::NAMESPACE .$name);
        $cookie->deleteAndUnset();
    }
}