<?php

namespace CartBoss\Api\Resources;

use CartBoss\Api\Resources\Addresses\BillingAddress;
use CartBoss\Api\Utils;

class Contact extends BillingAddress implements PayloadInterface
{
    /**
     * @var string|null
     */
    private $ip_address;
    /**
     * @var string|null
     */
    private $user_agent;
    /**
     * @var bool
     */
    private $accepts_marketing = false;

    public function __construct()
    {
        $this->ip_address = Utils::get_visitor_ip();
        $this->user_agent = Utils::get_user_agent();
    }

    public function getPayload(): array
    {
        return array_merge(parent::getPayload(), array(
            'ip_address' => $this->getIpAddress(),
            'user_agent' => $this->getUserAgent(),
            'accepts_marketing' => $this->getAcceptsMarketing(),
        ));
    }

    /**
     * @return string|null
     */
    public function getIpAddress(): ?string
    {
        return $this->ip_address;
    }

    /**
     * @param string|null $ip_address
     */
    public function setIpAddress(?string $ip_address): void
    {
        $this->ip_address = $ip_address;
    }

    /**
     * @return string|null
     */
    public function getUserAgent(): ?string
    {
        return $this->user_agent;
    }

    /**
     * @return bool
     */
    public function getAcceptsMarketing(): bool
    {
        return $this->accepts_marketing;
    }

    /**
     * @param bool $accepts_marketing
     */
    public function setAcceptsMarketing(bool $accepts_marketing): void
    {
        $this->accepts_marketing = $accepts_marketing;
    }

}