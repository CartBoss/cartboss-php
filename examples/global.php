<?php


use CartBoss\Api\CartBoss;
use CartBoss\Api\Encryption;
use CartBoss\Api\Resources\Attribution;
use CartBoss\Api\Resources\Contact;
use CartBoss\Api\Resources\Coupon;
use CartBoss\Api\Storage\ContextStorage;
use CartBoss\Api\Storage\CookieStorage;

require_once __DIR__ . '/../cartboss-php.php';

const CB_API_KEY = 'GrpYQV3GGgUYMk4JIhJ2TPoC6GEHP7Tk6ApwiyGYtGdj76UnnfQiHYtzSqUM9kk4';
//const CB_API_KEY = 'wr6p8q6Oqfe39ZzR07QJnqVkzFDNCKwxHH2gw1KskmQiG7pOj69V9XHzf61VQ3N2';
const FAKE_IP_ADDRESS = '127.0.0.1';

const PRIVATE_KEY = 'some long string';

const ATTRIBUTION_TOKEN = 'attribution_token';
const COUPON = 'coupon';
const CONTACT = 'contact';

// assume this is your visitor's order object/array/active-record, it depends on your business logic
$my_order = array(
    'id' => '1', // Database id (primary key for example)
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

$my_coupons = array(
    array(
        'id' => '1',
        'code' => 'CART20OFF',
        'type' => 'percentage',
        'value' => 20
    ),
    array(
        'id' => '2',
        'code' => 'VIPXXL',
        'type' => 'percentage',
        'value' => 50
    ),
);

$cartboss = new CartBoss(CB_API_KEY, true);

/*
 * Attribution token is essential information that links CartBoss message to an actual Purchase
 * Attribution token gets automatically injected into all SMS urls and should be used with Purchase event when available
 */
$cartboss->onAttributionIntercepted(function (Attribution $attribution) {
    // Debug: Store it to ContextStorage and display in checkout html
    ContextStorage::set(ATTRIBUTION_TOKEN, $attribution->getToken());

    //////////////
    // Option 1: attach it to your order/cart DB/session record
    //////////////

    global $my_order;
    $my_order['cb_attribution_token'] = $attribution->getToken();

    //////////////
    // Option 2: use Cookie Storage helper, to securely store it to visitor's browser cookie for 7 days
    //////////////
    ///
    CookieStorage::set(ATTRIBUTION_TOKEN, $attribution->getToken(), 60 * 60 * 24 * 7);

});

/*
 * Coupon is an object that holds: discount_code, discount_value, etc
 * Coupon/Discount info is injected into all urls that point back to your website, if SMS is set to offer a discount eg: "Hi, you got 20% off. Click here <url>"
 */
$cartboss->onCouponIntercepted(function (Coupon $coupon) {
    // Debug: Store it to ContextStorage and display in checkout html
    ContextStorage::set(COUPON, $coupon->getPayload());


    //////////////
    // Option 1: Immediately create coupon if doesn't exist and attach it to visitors cart/order
    //////////////

    // Step 1: Check if coupon exists in your DB
    global $my_coupons, $my_order;
    if (!in_array($coupon->getCode(), $my_coupons)) {
        // DB new coupon insert
        // tip: clean CartBoss-generated coupons regularly with a custom cron job every 24 hrs
    }

    // Step 2: attach it to your order/cart DB/session record
    $my_order['coupon_code'] = $coupon->getCode();


    //////////////
    // Option 2: Save it to cookie and inject directly into checkout coupon field
    //////////////

    // Step 0:
    $coupon_array = $coupon->getPayload();

    // Step 1: encrypt coupon and store it to cookie
    $encrypted_coupon_data = Encryption::encrypt(PRIVATE_KEY, $coupon_array); // you can skip this step if you don't want to encrypt cookie data
    CookieStorage::set('coupon_data', $encrypted_coupon_data, 60 * 60 * 24 * 31);

    // Step 2: decrypt stored data and use it when applicable
    $encrypted_coupon_data = CookieStorage::get('coupon_data');
    $coupon_array = Encryption::decrypt(PRIVATE_KEY, $encrypted_coupon_data);

    // Step 3: make sure you clear cookie once used
    CookieStorage::delete('coupon_data');

    // Step 4: attach it to your order/cart DB/session record
    $my_order['coupon_code'] = $coupon_array[Coupon::CODE];
});

/*
 * Contact is an object that holds information like: first_name, phone, etc.
 * Contact info is injected into all urls that point to your website.
 * You can use this info to re-populate billing address, whenever person visits your store through SMS
 */
$cartboss->onContactIntercepted(function (Contact $contact) {
    // Debug: Store it to ContextStorage and display in checkout html
    ContextStorage::set(CONTACT, $contact->getPayload());

    //////////////
    // Option 2: Save it to cookie and inject directly into checkout coupon field
    //////////////

    // Step 0:
    $contact_array = $contact->getPayload();

    // Step 1: encrypt contact and store it to cookie
    $encrypted_contact_data = Encryption::encrypt(PRIVATE_KEY, $contact_array); // you can skip this step if you don't want to encrypt cookie data
    CookieStorage::set('contact_data', $encrypted_contact_data, 60 * 60 * 24 * 365);

    // Step 2: decrypt stored data and use it when applicable
    $encrypted_contact_data = CookieStorage::get('contact_data');
    $contact_array = Encryption::decrypt(PRIVATE_KEY, $encrypted_contact_data); // skip this step if you skipped encrypt step above

    // Step 3: push contact data to checkout html and populate billing fields

});
