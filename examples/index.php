<?php

use CartBoss\Api\Encryption;
use CartBoss\Api\Interceptors\AttributionInterceptor;
use CartBoss\Api\Interceptors\ContactInterceptor;
use CartBoss\Api\Interceptors\CouponInterceptor;
use CartBoss\Api\Resources\Coupon;
use CartBoss\Api\Storage\CookieStorage;
use CartBoss\Api\Utils;

require __DIR__ . '/global.php';

if (Utils::getArrayValue($_GET, 'reset') === '1') {
    CookieStorage::delete(COOKIE_CONTACT);

    header("Location: /");
    exit;
}

$generate = Utils::getArrayValue($_GET, 'generate');
if ($generate == 'attribution') {
    header("Location: ?" . AttributionInterceptor::QUERY_VAR . "=" . Utils::getRandomString());
    exit;

} elseif ($generate == 'coupon') {
    $data = array(
        CouponInterceptor::STRUCT_KEY_CODE => Utils::getRandomString(6),
        CouponInterceptor::STRUCT_KEY_TYPE => array_rand(array_flip(array(Coupon::TYPE_CUSTOM, Coupon::TYPE_FIXED_AMOUNT, Coupon::TYPE_FREE_SHIPPING, Coupon::TYPE_PERCENTAGE)), 1),
        CouponInterceptor::STRUCT_KEY_VALUE => rand(0, 100)
    );

    $encoded = Encryption::encrypt(CB_API_KEY, $data);

    header("Location: ?" . CouponInterceptor::QUERY_VAR . "=" . $encoded);
    exit;
}

display("templates/checkout.php");

