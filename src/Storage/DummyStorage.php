<?php

namespace CartBoss\Api\Storage;

use CartBoss\Api\Interfaces\StorageInterface;
use CartBoss\Api\Utils;

class DummyStorage implements StorageInterface
{
    private $values = array();

    public function set($name, $value, $max_age = 60 * 60 * 24)
    {
        $this->values[$name] = $value;
    }

    public function get($name, $default = null)
    {
        return Utils::get_array_value($this->values, $name, $default);
    }

    public function delete($name)
    {
        unset($this->values[$name]);
    }
}