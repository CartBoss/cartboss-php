<?php

namespace CartBoss\Api\Interfaces;

interface StorageInterface
{
    /**
     * @param string $name
     * @param mixed $value
     * @param int $max_age
     * @return mixed
     */
    public function set($name, $value, $max_age = 60 * 60 * 24);

    /**
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function get($name, $default = null);

    /**
     * @param $name
     * @return void
     */
    public function delete($name);
}