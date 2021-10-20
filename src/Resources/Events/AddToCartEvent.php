<?php

namespace CartBoss\Api\Resources\Events;

class AddToCartEvent extends OrderBaseEvent
{
    const EVENT_NAME = 'AddToCart';

    public function __construct()
    {
        parent::__construct(array('checkout_url' => 'url:http,https'));
    }
}