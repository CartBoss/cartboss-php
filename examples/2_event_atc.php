<?php

/*
 * Each time visitor updates one of the checkout fields, this script is called through AJAX
 * Script creates AddToCart event and sends it to CartBoss server
 * Response is 204 empty or 400-500 ... in production you might want to log the error
 */

use CartBoss\Api\Exceptions\ApiException;
use CartBoss\Api\Exceptions\ValidationException;

require_once __DIR__ . '/../cartboss-php.php';

$cartboss = new \CartBoss\Api\CartBoss("");

$contact = new \CartBoss\Api\Resources\Contact();
$contact->setPhone(\CartBoss\Api\Utils::get_array_value($_POST, 'phone'));
$contact->setEmail(\CartBoss\Api\Utils::get_array_value($_POST, 'email'));
$contact->setIpAddress('...'); // you can skip this setter, for auto IP detection
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
$order->setId('...'); // this is unique order id, can be actual internal order id, md5(order_id), as long as it uniquely represents one exact order
$order->setValue(19.98); // total order value
$order->setCurrency('EUR'); // order currency
$order->setIsCod(true);
$order->setCheckoutUrl('https://yoursite/cartboss-order-restore?order_id=foo&x=1&bar=baz'); // link to restore_cart.php with order_id (same as ->setId(...))

// ... iterate through all cart lines
$cart_item = new \CartBoss\Api\Resources\CartItem();
$cart_item->setName('Product #1'); // required for sms personalization
$cart_item->setId('P1'); // product id
$cart_item->setVariationId("V9"); // product variation id
$cart_item->setPrice(9.99); // product price [quantity = 1]
$cart_item->setQuantity(2); // quantity
$order->addCartItem($cart_item);

try {
    // create ATC event
    $event = new \CartBoss\Api\Resources\Events\AddToCartEvent();
    // attach contact
    $event->setContact($contact);
    // attach order
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
}
