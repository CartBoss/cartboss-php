<?php

use CartBoss\Api\Utils;

require_once __DIR__ . '/global.php';
global $my_order;

/*
 * This script will restore abandoned cart and attach it to new browser's session.
 *
 * Keep in mind:
 * 1. Browsers do not share cookie storage. For example, user will abandon cart within Facebook app.
 * 2. Clicking SMS link will open the link in phone's default browser, which knows nothing about previous (Facebook) session.
 */

// assuming you set order_id=123 to checkout_url when sending ATC event
$order_id = Utils::get_array_value($_GET, 'order_id');

// no id?
if (empty($order_id)) {
    return "to home page";
}

// select order from your DB (be sure to prepare sql statement)
// $order = "SELECT * FROM orders WHERE id = $order_id)
$order = $my_order;

// doesn't exist?
if (!$order) {
    return "to home page";
}

// not abandoned?
if ($order['state'] != 'abandoned') {
    return "to home page";
}

// depending on your business logic, link order to visitors browser session
$_SESSION['order_id'] = $order['id'];

// redirect to checkout
return "to checkout";

