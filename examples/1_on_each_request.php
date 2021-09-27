<?php

require_once __DIR__ . '/../cartboss-php.php';

/*
 * CartBoss SDK offers three helper methods to parse and decode various information, injected into all SMS urls that point back to your website.
 *
 * It is recommended to run logic below, at each GET request.
 */

$cartboss = new \CartBoss\Api\CartBoss("");


/*
 * Attribution token is automatically injected into all urls that point back to your website.
 * You will need to attach this token to PurchaseEvent later on in the flow AND remove it from session|cookie|order once the purchase event is successfully sent out.
 *
 * It is recommended to check for the token != null and store it to session|cookie|order.
 *
 * Use cases:
 * - a person receives SMS with a link to her abandoned cart, if order is later placed, you'll attach this token to Purchase event.
 * - a person receives SMS with a link to a specific product page, ...
 * - a person receives SMS with a link to store home page, ...
 *
 */

if ($cartboss->getAttributionToken()) {
    echo "ATTRIBUTION TOKEN: " . $cartboss->getAttributionToken() . PHP_EOL;

    // store token until store/visitor order is initialized
    $_SESSION['cb_attribution_token'] = $cartboss->getAttributionToken();

    // store to database

    // store to cookie, expiration 7 days
}

/*
 * Contact is an object that holds information like: first_name, phone, etc.
 * Contact info is injected into all urls that point back to your website.
 * You can use this info to re-populate billing address, whenever person visits your store.
 * Keep in mind, Contact object is not intended to use for order restoration purposes.
 *
 * It is recommended to check for contact != null, and store it to session|cookie for later use.
 *
 * Use cases:
 * - a person receives SMS with a link to a specific product page
 * - a person receives SMS with a link to store home page
 *
 * When person places an item into her cart, you can use this info to fill out billing/shipping address fields and increase conversion rate.
 */

$contact = $cartboss->getContact();
if ($contact) {
    echo "CONTACT INFO" . PHP_EOL;

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

    // example
    $_SESSION['cb_contact'] = serialize($cartboss->getAttributionToken());

    //store to cookie, expiration 1Y
}


/*
 * Coupon is an object that holds information discount_code, discount_value, etc
 * Coupon/Discount info is injected into all urls that point back to your website, if SMS is set to offer a discount eg: "Hi, you got 20% off. Click here <url>"
 * You'll need to attach discount to an order, once order is created by your business logic.
 *
 * It is recommended to check for coupon != null, and store it to session|cookie|order for later use.
 *
 * Use cases:
 * - a person receives SMS with a link to her abandoned cart with 20% off
 * - a person receives SMS with a link to a specific product page with 15% off
 * - a person receives SMS with a link to store home page with 5% off
 *
 */

$coupon = $cartboss->getCoupon();
if ($coupon) {
    echo "COUPON" . PHP_EOL;

    echo $coupon->getCode() . PHP_EOL;
    echo $coupon->getType() . PHP_EOL;
    echo $coupon->getValue() . PHP_EOL;
    echo $coupon->isFixedAmount() . PHP_EOL;
    echo $coupon->isFreeShipping() . PHP_EOL;
    echo $coupon->isPercentage() . PHP_EOL;

    // 1. insert coupon to your DB
    // 2. remove it after 7 days (cron)
    // 3. attach it to visitor's order once initialized
}