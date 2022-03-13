<?php
/*
 * Sample order/cart data
 *
 * This variable represents a mocked up object of visitor's active cart/order record/table/...
 */

$store_order = array(
    'id' => '1', // DB primary key
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

/*
 * Sample store coupon table
 */
$store_coupons = array(
    array(
        'id' => '1',
        'code' => 'CART20OFF',
        'type' => 'percentage',
        'value' => 20
    ),
    array(
        'id' => '2',
        'code' => 'vipxxl',
        'type' => 'percentage',
        'value' => 50
    ),
);