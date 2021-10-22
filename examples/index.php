<?php

use CartBoss\Api\Encryption;
use CartBoss\Api\Interceptors\AttributionInterceptor;
use CartBoss\Api\Interceptors\ContactInterceptor;
use CartBoss\Api\Interceptors\CouponInterceptor;
use CartBoss\Api\Utils;

require __DIR__ . '/global.php';
require __DIR__ . '/utils.php';


$generate = Utils::get_array_value($_GET, 'generate');
if ($generate == 'attribution') {
    header("Location: ?" . AttributionInterceptor::QUERY_VAR . "=" . Utils::get_random_string());
    exit;

} elseif ($generate == 'coupon') {
    $data = array(
        CouponInterceptor::STRUCT_KEY_CODE => Utils::get_random_string(6),
        CouponInterceptor::STRUCT_KEY_TYPE => 'percentage',
        CouponInterceptor::STRUCT_KEY_VALUE => 25
    );

    $encoded = Encryption::encrypt(CB_API_KEY, $data);

    header("Location: ?" . CouponInterceptor::QUERY_VAR . "=" . $encoded);
    exit;

} elseif ($generate == 'contact') {
    $data = array(
        ContactInterceptor::STRUCT_KEY_PHONE => '01234567891',
        ContactInterceptor::STRUCT_KEY_EMAIL => 'test@example.com',
        ContactInterceptor::STRUCT_KEY_FIRST_NAME => 'Mike',
        ContactInterceptor::STRUCT_KEY_LAST_NAME => 'Boss',
        ContactInterceptor::STRUCT_KEY_ADDRESS_1 => 'Some street ',
        ContactInterceptor::STRUCT_KEY_ADDRESS_2 => 'Block 3',
        ContactInterceptor::STRUCT_KEY_COMPANY => 'CartBoss Ltd.',
        ContactInterceptor::STRUCT_KEY_CITY => 'Boss town',
        ContactInterceptor::STRUCT_KEY_STATE => 'AE',
        ContactInterceptor::STRUCT_KEY_POSTAL_CODE => '123456',
        ContactInterceptor::STRUCT_KEY_COUNTRY => 'SI',
    );

    $encoded = Encryption::encrypt(CB_API_KEY, $data);

    header("Location: ?" . ContactInterceptor::QUERY_VAR . "=" . $encoded);
    exit;
}


display("templates/checkout.php");

