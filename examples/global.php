<?php


require_once __DIR__ . '/../cartboss-php.php';

const CB_API_KEY = 'GrpYQV3GGgUYMk4JIhJ2TPoC6GEHP7Tk6ApwiyGYtGdj76UnnfQiHYtzSqUM9kk4';
const IP_ADDRESS = '127.0.0.1';

const ATTRIBUTION_TOKEN = 'attribution_token';

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

$cartboss = new \CartBoss\Api\CartBoss(CB_API_KEY);

/*
 * Attribution token is essential information that links CartBoss message to an actual Purchase
 * Attribution token gets automatically injected into all SMS urls and should be used with Purchase event when available
 */
$cartboss->onAttributionIntercepted(function (\CartBoss\Api\Resources\Attribution $attribution) {
    // option 1: store it to session|cookie and assign to PurchaseEvent
    // option 2: store it to order (DB)

    // debug
    var_dump($attribution->getToken());

    // you can use CB utility cookie storage and use it later when applicable
    \CartBoss\Api\Storage\CookieStorage::set(ATTRIBUTION_TOKEN, $attribution->getToken(), 60 * 60 * 24 * 7);
});

/*
 * Coupon is an object that holds: discount_code, discount_value, etc
 * Coupon/Discount info is injected into all urls that point back to your website, if SMS is set to offer a discount eg: "Hi, you got 20% off. Click here <url>"
 */
$cartboss->onCouponIntercepted(function (\CartBoss\Api\Resources\Coupon $coupon) {
    // option 1: insert it to DB + store coupon id|code to session|cookie
    // option 2: insert it to DB + attach it to current order
    // option 3: serialize, store to session|cookie, attach to order when created

    echo $coupon->getCode() . PHP_EOL;
    echo $coupon->getType() . PHP_EOL;
    echo $coupon->getValue() . PHP_EOL;
    echo $coupon->isFixedAmount() . PHP_EOL;
    echo $coupon->isFreeShipping() . PHP_EOL;
    echo $coupon->isPercentage() . PHP_EOL;
    echo $coupon->isCustom() . PHP_EOL;

    // you can use CB utility cookie storage and use it later when applicable
    \CartBoss\Api\Storage\CookieStorage::set('coupon_code', $coupon->getCode(), 60 * 60 * 24 * 31);
});

/*
 * Contact is an object that holds information like: first_name, phone, etc.
 * Contact info is injected into all urls that point to your website.
 * You can use this info to re-populate billing address, whenever person visits your store through SMS
 */
$cartboss->onContactIntercepted(function (\CartBoss\Api\Resources\Contact $contact) {
    // option 1: insert it to DB + store coupon id|code to session|cookie
    // option 2: insert it to DB + attach it to current order
    // option 3: store to session|cookie, then insert into DB + attach to order

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

    // -- store to cookie --
    // you can use CB utility encryption class to encode array with a secret
    // $encoded_contact_data = \CartBoss\Api\Encryption::encode(CB_API_KEY, $contact->getPayload());

    // use CB utility cookie storage class to store encrypted data for later use [eg checkout page]
    // \CartBoss\Api\Storage\CookieStorage::set('contact', $encoded_contact_data, 60 * 60 * 24 * 365);

    // -- get from cookie --
    // at checkout page; retrieve encoded contact data
    // $cookie_data = \CartBoss\Api\Storage\CookieStorage::get('contact');

    // decode it to array
    // $decoded_contact_data = \CartBoss\Api\Encryption::decode(CB_API_KEY, $cookie_data);
    // var_dump($decoded_contact_data);

});


//$cartboss_session = $cartboss->getSession();
//echo "CARTBOSS SESSION TOKEN: " . $cartboss_session->getToken() . PHP_EOL;


