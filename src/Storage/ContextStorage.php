<?php

namespace CartBoss\Api\Storage;

use CartBoss\Api\Interfaces\StorageInterface;
use CartBoss\Api\Utils;

class ContextStorage implements StorageInterface
{
    private static $values = array();

    public static function set($name, $value, $max_age=0)
    {
        self::$values[$name] = $value;
    }

    public static function get($name, $default = null)
    {
        return Utils::get_array_value(self::$values, $name, $default);
    }

    public static function delete($name)
    {
        unset(self::$values[$name]);
    }
}