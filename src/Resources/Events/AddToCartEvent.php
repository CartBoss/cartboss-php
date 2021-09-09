<?php

namespace CartBoss\Api\Resources\Events;

class AddToCartEvent extends OrderBaseEvent
{
    public function __construct()
    {
        parent::__construct('AddToCart');
    }
}