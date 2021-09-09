<?php

namespace CartBoss\Api\Resources\Events;

class PurchaseEvent extends OrderBaseEvent
{
    public function __construct()
    {
        parent::__construct('Purchase');
    }
}