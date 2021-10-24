<?php

use CartBoss\Api\Utils;

require_once __DIR__ . '/global.php';
global $store_order;

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

// Debug
var_dump($order);