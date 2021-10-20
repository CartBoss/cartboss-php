<?php

namespace CartBoss\Api\Resources\Events;

class PurchaseEvent extends OrderBaseEvent
{
    const EVENT_NAME = 'Purchase';
    public function __construct()
    {
        parent::__construct(array('checkout_url' => 'url:http,https'));
    }
}