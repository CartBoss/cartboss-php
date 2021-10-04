<?php

use CartBoss\Api\Utils;

require_once __DIR__ . '/../cartboss-php.php';

$secret = "01234567890123456789012345678912";
$text = "hello space";

var_dump(Utils::aes_encode($secret, $text));