<?php

use CartBoss\Api\Exceptions\ApiException;
use CartBoss\Api\Exceptions\ValidationException;

require_once __DIR__ . '/../cartboss-php.php';

$cartboss = new \CartBoss\Api\CartBoss("");

$contact = new \CartBoss\Api\Resources\Contact();
$contact->setPhone(\CartBoss\Api\Utils::get_array_value($_POST, 'phone'));
$contact->setEmail(\CartBoss\Api\Utils::get_array_value($_POST, 'email'));
$contact->setIpAddress('...'); // you can omit this setter, for auto IP detection
$contact->setAcceptsMarketing(true); // if "I want to receive news and promotions" checkbox is visible and checked || varies by store policy how to handle this field

$contact->setFirstName(\CartBoss\Api\Utils::get_array_value($_POST, 'first_name'));
$contact->setLastName(\CartBoss\Api\Utils::get_array_value($_POST, 'last_name'));
$contact->setAddress1(\CartBoss\Api\Utils::get_array_value($_POST, 'address_1'));
$contact->setAddress2(\CartBoss\Api\Utils::get_array_value($_POST, 'address_2'));
$contact->setCompany(\CartBoss\Api\Utils::get_array_value($_POST, 'company'));
$contact->setCity(\CartBoss\Api\Utils::get_array_value($_POST, 'city'));
$contact->setPostalCode(\CartBoss\Api\Utils::get_array_value($_POST, 'zip'));
$contact->setState(\CartBoss\Api\Utils::get_array_value($_POST, 'state'));
$contact->setCountry(\CartBoss\Api\Utils::get_array_value($_POST, 'country_code')); // can be billing address country code || geo

$order = new \CartBoss\Api\Resources\Order();
$order->setId('...'); // this is unique order id, can be actual order id, md5(order_id), as long as it uniquely represents one exact order
$order->setValue(19.99); // total order value
$order->setCurrency('EUR'); // order currency
$order->setIsCod(true);

$order->setNumber('...'); // order number once placed (eg: #128732)

// ... iterate through all cart lines
$cart_item = new \CartBoss\Api\Resources\CartItem();
$cart_item->setName('Product #1'); // required for sms personalization
$cart_item->setId('P1');
$cart_item->setVariationId("V9");
$cart_item->setPrice(14.99);
$cart_item->setQuantity(1);
$order->addCartItem($cart_item);


try {
    // create Purchase event
    $event = new \CartBoss\Api\Resources\Events\PurchaseEvent();

    /*
     * Attribution token tells CartBoss purchase was made as a result of clicking CartBoss link (SMS).
     * If no attribution token is attached to purchase event, CartBoss will exclude it from statistics, but will still be able to send additional post-purchase messages (if defined)
     *
     * Please see 1_on_each_request.php for more info.
     */
    $event->setAttribution('...');

    // attach contact and order
    $event->setContact($contact);
    $event->setOrder($order);

    // send it to CartBoss, if invalid or request fails, exception is thrown
    $cartboss->sendOrderEvent($event);
    echo "event {$event->getEventName()} successfully sent";

} catch (ValidationException $e) {
    echo "<h1>Event validation failed</h1>";
    var_dump($e->getMessage());

} catch (ApiException $e) {
    echo "<h1>Api failed</h1>";
    var_dump($e->getMessage());

} finally {

    // don't forget to clear attribution token
    unset($_SESSION['cb_attribution_token']);
}