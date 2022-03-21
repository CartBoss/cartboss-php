<?php

use CartBoss\Api\CartBoss;
use CartBoss\Api\Resources\Attribution;
use CartBoss\Api\Resources\Coupon;
use CartBoss\Api\Storage\ContextStorage;
use CartBoss\Api\Storage\CookieStorage;
use CartBoss\Api\Utils;

// include CartBoss SDK library
require_once __DIR__ . '/../cartboss-php.php';

// include example helper methods
require __DIR__ . '/utils.php';

/*
 * Your CartBoss API key.
 *
 * Each domain requires its unique API key. You can create one at https://app.cartboss.io/sites
 * You can also use a service such as https://ngrok.com/. It allows you to create a tunnel from a public host to your local host to facilitate the process of testing and implementation.
 *
 */
const CB_API_KEY = '1111111111111111111111111111111111111111111111111111111111111111';

// visitor's ip address (fake)
const FAKE_IP_ADDRESS = '127.0.0.1';

// template var keys (only for this example needs)
const TMPL_ATTRIBUTION_TOKEN = 'TMPL_ATTRIBUTION_TOKEN';
const TMPL_COUPON = 'TMPL_COUPON';
const TMPL_CONTACT = 'TMPL_CONTACT';
const TMPL_EVENT_PAYLOAD = 'TMPL_EVENT_PAYLOAD';
const TMPL_EVENT_ERROR = 'TMPL_EVENT_ERROR';

const COOKIE_ATTRIBUTION_TOKEN = 'attribution_token';
const COOKIE_CONTACT = 'contact';

/*
 * Sample order/cart data
 *
 * This variable represents a mocked up object of visitor's active cart/order record/table/...
 */
$store_order = array(
    'id' => '1', // DB primary key
    'value' => 40.0,
    'currency' => 'EUR',
    'state' => 'abandoned',
    'number' => '#12345',
    'method' => 'COD',
    'cart_items' => array(
        array(
            'id' => 'P1',
            'variation_id' => 'P1V3',
            'name' => 'Product 1',
            'price' => 10.0,
            'quantity' => 2,
        ),
        array(
            'id' => 'P2',
            'variation_id' => null,
            'name' => 'Product 2',
            'price' => 20.0,
            'quantity' => 1,
        )
    ),
    // ...
);

/*
 * Sample store coupon table
 */
$store_coupons = array(
    array(
        'id' => '1',
        'code' => 'CART20OFF',
        'type' => 'percentage',
        'value' => 20
    ),
    array(
        'id' => '2',
        'code' => 'vipxxl',
        'type' => 'percentage',
        'value' => 50
    ),
);

$cartboss = new CartBoss(CB_API_KEY, true);

/*
 * CartBoss attribution token is a hash string, injected into all SMS urls by CartBoss platform.
 * Your objective is to parse and attach it to a PurchaseEvent when sent back to CartBoss platform.
 * If you omit using attribution token, CartBoss statistics will not work correctly.
 */
$cartboss->onAttributionIntercepted(function(Attribution $attribution) {
    // Debug: Store it to ContextStorage and display in checkout html
    ContextStorage::set(TMPL_ATTRIBUTION_TOKEN, $attribution->getToken());

    // Step 1: Store attribution token to browser's cookie for 30 days
    CookieStorage::set(COOKIE_ATTRIBUTION_TOKEN, $attribution->getToken(), 60 * 60 * 24 * 30);

    // Step 2 @ event_purchase.php: Attach attribution token to a purchase event (see event_purchase.php)

    // Step 3 @ event_purchase.php: If event succeeded, clear token cookie

});

/*
 * Coupon is an object that holds: discount_code, discount_value, etc
 * Coupon/Discount info is injected into all urls that point back to your website, if SMS is set to offer a discount eg: "Hi, you got 20% off. Click here <url>"
 */
$cartboss->onCouponIntercepted(function(Coupon $coupon) {
    // Debug: Store it to ContextStorage and display in checkout html
    ContextStorage::set(TMPL_COUPON, $coupon->getPayload());

    // Step 1: Check if coupon exists in your DB
    global $store_coupons, $store_order;
    if (!in_array($coupon->getCode(), $store_coupons)) {
        // Step 2: insert coupon (DB)
        // pro-tip: clean CartBoss-generated coupons regularly with a custom cron job every 24 hrs
    }

    // Step 3: Check if store_order doesn't already use the coupon
    if (Utils::getArrayValue($store_order, 'coupon_code') != $coupon->getCode()) {
        // Step 4: attach it to your order/cart DB/session record
        $store_order['coupon_code'] = $coupon->getCode();
        // ...
    }
});
