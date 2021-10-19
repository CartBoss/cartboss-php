<?php


use CartBoss\Api\Interceptors\ContactUrlInterceptor;
use CartBoss\Api\Interceptors\CouponUrlInterceptor;

require_once __DIR__ . '/../cartboss-php.php';

const CB_API_KEY = 'GrpYQV3GGgUYMk4JIhJ2TPoC6GEHP7Tk6ApwiyGYtGdj76UnnfQiHYtzSqUM9kk4';
const IP_ADDRESS = '127.0.0.1';

// assume this is your visitor's order object/array/active-record, it depends on your business logic
$my_order = array(
    'internal_id' => '...', // Database id (primary key for example)
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
 * CartBoss SDK offers three helper methods to parse and decode various information, injected into all SMS urls that point to your website
 */

$cartboss = new \CartBoss\Api\CartBoss(CB_API_KEY);

$cartboss_session = new \CartBoss\Api\Managers\Session();
echo "CARTBOSS SESSION TOKEN: " . $cartboss_session->getToken() . PHP_EOL;

/*
 * Attribution token is essential information that links CartBoss message to an actual Purchase
 * Attribution token gets automatically injected into all SMS urls and should be used with Purchase event if available
 */

$attribution_interceptor = new \CartBoss\Api\Interceptors\AttributionUrlInterceptor();
if ($attribution_interceptor->getToken()) {
    echo "INTERCEPTED ATTRIBUTION TOKEN: " . $attribution_interceptor->getToken() . PHP_EOL;

    // once token is intercepted, store it to active order or browser cookie for later use (Purchase Event)

    // ... visitor places an order with your store

    // create Purchase event and $event->setAttribution( ... stored attribution token ...)
}

/*
 * Contact is an object that holds information like: first_name, phone, etc.
 * Contact info is injected into all urls that point to your website.
 * You can use this info to re-populate billing address, whenever person visits your store through SMS
 */

$contact_interceptor = new ContactUrlInterceptor(CB_API_KEY);
$contact = $contact_interceptor->getContact();
if ($contact) {
    echo "INTERCEPTED CONTACT INFO" . PHP_EOL;

    echo $contact->getPhone() . PHP_EOL;
    echo $contact->getEmail() . PHP_EOL;
    echo $contact->getFirstName() . PHP_EOL;
    echo $contact->getLastName() . PHP_EOL;
    echo $contact->getAddress1() . PHP_EOL;
    echo $contact->getAddress2() . PHP_EOL;
    echo $contact->getCompany() . PHP_EOL;
    echo $contact->getState() . PHP_EOL;
    echo $contact->getCity() . PHP_EOL;
    echo $contact->getPostalCode() . PHP_EOL;
    echo $contact->getCountry() . PHP_EOL;

    // once contact info is intercepted, you can use it to pre-populate checkout fields
}


/*
 * Coupon is an object that holds information discount_code, discount_value, etc
 * Coupon/Discount info is injected into all urls that point back to your website, if SMS is set to offer a discount eg: "Hi, you got 20% off. Click here <url>"
 */

$coupon_interceptor = new CouponUrlInterceptor(CB_API_KEY, $attribution_interceptor->getToken());
$coupon = $coupon_interceptor->getCoupon();
if ($coupon) {
    echo "COUPON" . PHP_EOL;

    echo $coupon->getCode() . PHP_EOL;
    echo $coupon->getType() . PHP_EOL;
    echo $coupon->getValue() . PHP_EOL;
    echo $coupon->isFixedAmount() . PHP_EOL;
    echo $coupon->isFreeShipping() . PHP_EOL;
    echo $coupon->isPercentage() . PHP_EOL;

    // once coupon is intercepted, store it to your database and attach it to order once it's initialized (usually when first item is added to cart)

    // also, you might want to prune coupons after 7 days with a crontab script
}