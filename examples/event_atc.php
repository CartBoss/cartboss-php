<?php

use CartBoss\Api\CartBoss;
use CartBoss\Api\Exceptions\ApiException;
use CartBoss\Api\Exceptions\ValidationException;
use CartBoss\Api\Resources\CartItem;
use CartBoss\Api\Resources\Contact;
use CartBoss\Api\Resources\Events\AddToCartEvent;
use CartBoss\Api\Resources\Order;
use CartBoss\Api\Utils;

require_once __DIR__ . '/global.php';
global $cartboss;

$active_order = CURRENT_ORDER;

// test order depending on your business logic
if (!$active_order || $active_order['state'] != 'abandoned') {
    // nothing to report here, as order doesn't seem abandoned
    return;
}


$contact = new Contact();
$contact->setPhone(Utils::get_array_value($_POST, 'billing_phone'));
$contact->setEmail(Utils::get_array_value($_POST, 'billing_email'));
$contact->setAcceptsMarketing(Utils::get_array_value($_POST, 'accepts_marketing', false));
$contact->setIpAddress(IP_ADDRESS); // you can skip this setter for auto IP detection

$contact->setFirstName(Utils::get_first_non_empty_value(Utils::get_array_value($_POST, 'billing_first_name'), Utils::get_array_value($_POST, 'shipping_first_name')));
$contact->setLastName(Utils::get_first_non_empty_value(Utils::get_array_value($_POST, 'billing_last_name'), Utils::get_array_value($_POST, 'shipping_last_name')));
$contact->setAddress1(Utils::get_first_non_empty_value(Utils::get_array_value($_POST, 'billing_address_1'), Utils::get_array_value($_POST, 'shipping_address_1')));
$contact->setAddress2(Utils::get_first_non_empty_value(Utils::get_array_value($_POST, 'billing_address_2'), Utils::get_array_value($_POST, 'shipping_address_2')));
$contact->setCompany(Utils::get_first_non_empty_value(Utils::get_array_value($_POST, 'billing_company'), Utils::get_array_value($_POST, 'shipping_company')));
$contact->setCity(Utils::get_first_non_empty_value(Utils::get_array_value($_POST, 'billing_city'), Utils::get_array_value($_POST, 'shipping_city')));
$contact->setPostalCode(Utils::get_first_non_empty_value(Utils::get_array_value($_POST, 'billing_zip'), Utils::get_array_value($_POST, 'shipping_zip')));
$contact->setState(Utils::get_first_non_empty_value(Utils::get_array_value($_POST, 'billing_state'), Utils::get_array_value($_POST, 'shipping_state')));
$contact->setCountry(Utils::get_first_non_empty_value(Utils::get_array_value($_POST, 'billing_country'), Utils::get_array_value($_POST, 'shipping_country')));

$order = new Order();
$order->setId($cartboss->getSession()->getToken());
$order->setValue($active_order['value']); // total order value
$order->setCurrency($active_order['currency']); // order currency
$order->setIsCod($active_order['method'] == 'COD');
$order->setCheckoutUrl(Utils::getCurrentUrl() . "/3_restore_cart.php?order_id={$cartboss->getSession()->getToken()}");

foreach ($active_order['cart_items'] as $obj) {
    $cart_item = new CartItem();
    $cart_item->setName($obj['name']); // required for sms personalization
    $cart_item->setId($obj['id']); // product id
    $cart_item->setVariationId($obj['variation_id']); // product variation id
    $cart_item->setPrice($obj['price']); // product price [quantity = 1]
    $cart_item->setQuantity($obj['quantity']); // quantity

    $order->addCartItem($cart_item);
}

// create ATC event
$event = new AddToCartEvent();

// attach contact
$event->setContact($contact);

// attach order
$event->setOrder($order);

// debug
var_dump($event->getPayload());

try {
    $cartboss = new CartBoss(API_KEY);

    // send to CartBoss, if invalid or request fails, exception is thrown
    $cartboss->sendOrderEvent($event);

    echo "event {$event->getEventName()} successfully sent";

} catch (ValidationException $e) {

    // simply discard this event when validation error occurs
    echo "<h1>Event validation failed</h1>";
    var_dump($e->getMessage());

} catch (ApiException $e) {

    // you might want to implement some kind of retry mechanism
    echo "<h1>Api failed</h1>";
    var_dump($e->getMessage());
}
