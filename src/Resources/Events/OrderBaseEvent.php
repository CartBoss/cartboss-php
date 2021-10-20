<?php

namespace CartBoss\Api\Resources\Events;

use CartBoss\Api\Resources\Contact;
use CartBoss\Api\Resources\Order;
use CartBoss\Api\Interfaces\PayloadInterface;

abstract class OrderBaseEvent extends BaseEvent implements PayloadInterface
{
    /**
     * @var string|null
     */
    private $attribution_token = null;

    /**
     * @var Order|null
     */
    private $order;

    /**
     * @var Contact|null
     */
    private $contact;

    public function __construct($rules = array())
    {
        parent::__construct(array_merge(array(
            'contact' => 'required',
            'contact.ip_address' => 'required|ip',
            'contact.phone' => 'required|min:5',
            'contact.country' => 'required|between:2,2',

            'order' => 'required',
            'order.id' => 'required',
            'order.currency' => 'required',
            'order.value' => 'required|numeric',
            'order.is_cod' => 'boolean',
            'order.checkout_url' => 'required|url',

            'order.items' => 'required|array|min:1',
            'order.items.*.name' => 'required',
            'order.items.*.quantity' => 'min:1',
        ), $rules));
    }

    /**
     * @param string|null $attribution_token
     */
    public function setAttributionToken(?string $attribution_token): void
    {
        $this->attribution_token = $attribution_token;
    }

    /**
     * @return string|null
     */
    public function getAttributionToken(): ?string
    {
        return $this->attribution_token;
    }

    /**
     * @param Order|null $order
     */
    public function setOrder(?Order $order): void
    {
        $this->order = $order;
    }

    /**
     * @param Contact|null $contact
     */
    public function setContact(?Contact $contact): void
    {
        $this->contact = $contact;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        $data = parent::getPayload();

        $data['attribution'] = $this->attribution_token;

        if ($this->contact) {
            $data = array_merge($data, array(
                'contact' => $this->contact->getPayload()
            ));
        }

        if ($this->order) {
            $data = array_merge($data, array(
                'order' => $this->order->getPayload()
            ));
        }

        return $data;
    }

}