<?php

namespace CartBoss\Api\Resources;

use CartBoss\Api\Managers\Session;
use CartBoss\Api\Resources\Addresses\BillingAddress;
use CartBoss\Api\Resources\Addresses\ShippingAddress;
use League\Uri\Uri;
use League\Uri\UriModifier;

class Order
{
    /**
     * @var string|null
     */
    private $id;
    /**
     * @var string|null
     */
    private $number;
    /**
     * @var float
     */
    private $value;
    /**
     * @var string|null
     */
    private $currency;
    /**
     * @var string|null
     */
    private $checkout_url;
    /**
     * @var bool
     */
    private $is_cod = false;
    /**
     * @var CartItem[]|null
     */
    private $items = array();
    /**
     * @var array
     */
    private $metadata = null;
    /**
     * @var BillingAddress|null
     */
    private $billing_address;
    /**
     * @var ShippingAddress|null
     */
    private $shipping_address;

    /**
     * @return array
     */
    public function getCartItems(): array
    {
        return $this->items;
    }

    /**
     * @param CartItem $cart_item
     */
    public function addCartItem(CartItem $cart_item)
    {
        array_push($this->items, $cart_item);
    }

    /**
     * @return BillingAddress|null
     */
    public function getBillingAddress(): ?BillingAddress
    {
        return $this->billing_address;
    }

    /**
     * @param BillingAddress|null $billing_address
     */
    public function setBillingAddress(?BillingAddress $billing_address): void
    {
        $this->billing_address = $billing_address;
    }

    /**
     * @return ShippingAddress|null
     */
    public function getShippingAddress(): ?ShippingAddress
    {
        return $this->shipping_address;
    }

    /**
     * @param ShippingAddress|null $shipping_address
     */
    public function setShippingAddress(?ShippingAddress $shipping_address): void
    {
        $this->shipping_address = $shipping_address;
    }

    public function getPayload(): array
    {
        $data = array(
            'id' => $this->getId(),
            'number' => $this->getNumber(),
            'is_cod' => $this->getIsCod(),
            'value' => $this->getValue(),
            'currency' => $this->getCurrency(),
            'checkout_url' => $this->getCheckoutUrl(),
            'billing_address' => null,
            'shipping_address' => null,
            'metadata' => null,
            'items' => null
        );

        if (isset($this->metadata)) {
            $data['metadata'] = $this->metadata;
        }

        if (isset($this->billing_address)) {
            $data['billing_address'] = $this->billing_address->getPayload();
        }

        if (isset($this->shipping_address)) {
            $data['shipping_address'] = $this->shipping_address->getPayload();
        }

        if (!empty($this->items)) {
            $data['items'] = [];
            foreach ($this->items as $cart_item) {
                array_push($data['items'], array(
                    'id' => $cart_item->getId(),
                    'variation_id' => $cart_item->getVariationId(),
                    'name' => $cart_item->getName(),
                    'quantity' => $cart_item->getQuantity(),
                    'image_url' => $cart_item->getImageUrl(),
                    'price' => $cart_item->getPrice(),
                ));
            }
        }

        return $data;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * @param string|null $number
     */
    public function setNumber(?string $number): void
    {
        $this->number = $number;
    }

    /**
     * @return bool
     */
    public function getIsCod(): bool
    {
        return $this->is_cod;
    }

    /**
     * @param bool $is_cod
     */
    public function setIsCod(bool $is_cod): void
    {
        $this->is_cod = $is_cod;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @param float $value
     */
    public function setValue(float $value): void
    {
        $this->value = $value;
    }

    /**
     * @return string|null
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * @param string|null $currency
     */
    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return string|null
     */
    public function getCheckoutUrl(): ?string
    {
        $checkout_url = Uri::createFromString($this->checkout_url);

        $checkout_url = UriModifier::appendQuery($checkout_url, Session::QUERY_VAR . '=' . $this->getId());

        return $checkout_url->jsonSerialize();;
    }

    /**
     * @param string|null $checkout_url
     */
    public function setCheckoutUrl(?string $checkout_url): void
    {
        $this->checkout_url = $checkout_url;
    }
}
