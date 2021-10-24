<?php

use CartBoss\Api\Utils;

require_once __DIR__ . '/global.php';
global $store_order;

/*
 * This script will restore abandoned cart and attach it to new browser's session.
 *
 * Keep in mind:
 * 1. Browsers do not share cookie storage. For example, user will abandon cart within Facebook app.
 * 2. Clicking SMS link will open the link in phone's default browser, which knows nothing about previous (Facebook) session.
 */

// Step 1: Get provided order id from url
$order_id = Utils::getArrayValue($_GET, 'order_id');

if (empty($order_id)) {
    return "to home page";
}

// Step 2: Retrieve order/cart exists from your database
// $order = "SELECT * FROM orders WHERE id = $order_id
$order = $store_order;

// Step 3: Check whether order exists
if (!$order) {
    return "to home page";
}

// Step 4: Check whether order is still in abandoned state
if ($order['state'] !== 'abandoned') {
    return "to home page";
}

// Step 5: Restore visitor's session and redirect to checkout
