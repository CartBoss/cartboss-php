<?php

use CartBoss\Api\Exceptions\ApiException;

require_once __DIR__ . '/global.php';
global $cartboss;

try {
    var_dump($cartboss->ping());
} catch (ApiException $e) {
    echo "<h1>Api failed</h1>";
    var_dump($e->getMessage());
}