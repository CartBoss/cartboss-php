<?php

namespace CartBoss\Api\Resources\Events;

use CartBoss\Api\Interfaces\PayloadInterface;

abstract class BaseEvent implements PayloadInterface
{
    const EVENT_NAME = null;

    private $timestamp;
    private $rules = array(
        'event' => 'required',
        'platform' => 'required',
        'timestamp' => 'required|integer',
    );

    public function __construct($rules = array())
    {
        $this->timestamp = time();
        $this->rules = array_merge($this->rules, $rules);
    }

    public function getPayload(): array
    {
        return array(
            'platform' => 'API',
            'event' => static::EVENT_NAME,
            'timestamp' => $this->timestamp,
        );
    }

    public function getEventName(): string
    {
        return static::EVENT_NAME;
    }

    public function setTimestamp($value)
    {
        $this->timestamp = $value;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

}

