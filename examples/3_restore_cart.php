<?php

/*
 * This script will restore abandoned cart and attach it to new browser's session.
 *
 * Keep in mind:
 * 1. Browsers do not share cookie storage. For example, user will abandon cart within Facebook app.
 * 2. Clicking SMS link will open the link in phone's default browser, which knows nothing about previous (Facebook) session.
 */

require "../vendor/autoload.php";

$cartboss = new \CartBoss\Api\CartBoss("");

$order_id = \CartBoss\Api\Utils::get_array_value($_GET, 'order_id');

if (empty($order_id)) {
    // redirect to home
    return;

} else {
    // select order from your DB
    $order_data = array();

    // depending on your business logic, link order to visitors browser session

    // redirect to store checkout page
    return;
}