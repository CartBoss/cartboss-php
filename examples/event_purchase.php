<?php

use CartBoss\Api\Exceptions\ApiException;
use CartBoss\Api\Exceptions\EventValidationException;
use CartBoss\Api\Resources\CartItem;
use CartBoss\Api\Resources\Contact;
use CartBoss\Api\Resources\Events\PurchaseEvent;
use CartBoss\Api\Resources\Order;
use CartBoss\Api\Storage\ContextStorage;
use CartBoss\Api\Storage\CookieStorage;
use CartBoss\Api\Utils;

require_once __DIR__ . '/global.php';
global $cartboss, $store_order;

$active_order = $store_order;

// test order depending on your business logic
if (!$active_order || $active_order['state'] != 'abandoned') {
    // nothing to report here, as order doesn't seem abandoned
    return;
}

// create ATC event
$event = new PurchaseEvent();

// attach attribution token (if available for this order)
$attribution_token = CookieStorage::get(COOKIE_ATTRIBUTION_TOKEN, null); // when sending from frontend (ie; browser)
// $attribution_token = SELECT cb_attribution_token FROM orders WHERE order_id = $_SESSION['order_id'] // when sending from backend (ie; cron)
$event->setAttributionToken($attribution_token);

// contact section
$contact = new Contact();
$contact->setPhone(Utils::get_array_value($_POST, 'billing_phone'));
$contact->setEmail(Utils::get_array_value($_POST, 'billing_email'));
$contact->setAcceptsMarketing(Utils::get_array_value($_POST, 'accepts_marketing', false));
$contact->setIpAddress(FAKE_IP_ADDRESS); // you can skip this setter for auto IP detection

$contact->setFirstName(Utils::get_first_non_empty_value(Utils::get_array_value($_POST, 'billing_first_name'), Utils::get_array_value($_POST, 'shipping_first_name')));
$contact->setLastName(Utils::get_first_non_empty_value(Utils::get_array_value($_POST, 'billing_last_name'), Utils::get_array_value($_POST, 'shipping_last_name')));
$contact->setAddress1(Utils::get_first_non_empty_value(Utils::get_array_value($_POST, 'billing_address_1'), Utils::get_array_value($_POST, 'shipping_address_1')));
$contact->setAddress2(Utils::get_first_non_empty_value(Utils::get_array_value($_POST, 'billing_address_2'), Utils::get_array_value($_POST, 'shipping_address_2')));
$contact->setCompany(Utils::get_first_non_empty_value(Utils::get_array_value($_POST, 'billing_company'), Utils::get_array_value($_POST, 'shipping_company')));
$contact->setCity(Utils::get_first_non_empty_value(Utils::get_array_value($_POST, 'billing_city'), Utils::get_array_value($_POST, 'shipping_city')));
$contact->setPostalCode(Utils::get_first_non_empty_value(Utils::get_array_value($_POST, 'billing_zip'), Utils::get_array_value($_POST, 'shipping_zip')));
$contact->setState(Utils::get_first_non_empty_value(Utils::get_array_value($_POST, 'billing_state'), Utils::get_array_value($_POST, 'shipping_state')));
$contact->setCountry(Utils::get_first_non_empty_value(Utils::get_array_value($_POST, 'billing_country'), Utils::get_array_value($_POST, 'shipping_country')));

$event->setContact($contact);

// order section
$order = new Order();
$order->setId(sha1($active_order['id']));
$order->setValue($active_order['value']); // total order value
$order->setCurrency($active_order['currency']); // order currency
$order->setIsCod($active_order['method'] == 'COD');
$order->setNumber($active_order['number']); // order number

foreach ($active_order['cart_items'] as $obj) {
    $cart_item = new CartItem();
    $cart_item->setName($obj['name']); // required for sms personalization
    $cart_item->setId($obj['id']); // product id
    $cart_item->setVariationId($obj['variation_id']); // product variation id
    $cart_item->setPrice($obj['price']); // product price [quantity = 1]
    $cart_item->setQuantity($obj['quantity']); // quantity

    $order->addCartItem($cart_item);
}

$event->setOrder($order);

try {
    // debug
    ContextStorage::set(TMPL_EVENT_PAYLOAD, $event->getPayload());

    // send event to CartBoss API
    $cartboss->sendOrderEvent($event);
    echo "event {$event->getEventName()} successfully sent";

} catch (EventValidationException $e) {
    // debug
    ContextStorage::set(TMPL_EVENT_ERROR, $e->getMessage());

} catch (ApiException $e) {
    // debug
    ContextStorage::set(TMPL_EVENT_ERROR, $e->getMessage());
}

display("templates/confirmation.php");