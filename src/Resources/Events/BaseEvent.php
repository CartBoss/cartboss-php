<?php

namespace CartBoss\Api\Resources\Events;

use CartBoss\Api\Interfaces\PayloadInterface;

abstract class BaseEvent implements PayloadInterface
{
    private $event_name;
    private $timestamp;
    private $rules = array(
        'event' => 'required',
        'platform' => 'required',
        'timestamp' => 'required|integer',
    );

    public function __construct($event_name, $rules = array())
    {
        $this->event_name = trim($event_name);
        $this->timestamp = time();
        $this->rules = array_merge($this->rules, $rules);
    }

    public function getPayload(): array
    {
        return array(
            'platform' => 'API',
            'event' => $this->event_name,
            'timestamp' => $this->timestamp,
        );
    }

    public function getEventName(): string
    {
        return $this->event_name;
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

