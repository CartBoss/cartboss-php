<?php

namespace CartBoss\Api\Resources;

use CartBoss\Api\Resources\Addresses\BillingAddress;
use CartBoss\Api\Utils;
use Rakit\Validation\Validator;


class Contact extends BillingAddress {
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

    public function getPayload(): array {
        return array_merge(parent::getPayload(), array(
            'ip_address' => $this->getIpAddress(),
            'user_agent' => $this->getUserAgent(),
            'accepts_marketing' => $this->getAcceptsMarketing(),
        ));
    }

    /**
     * @return string|null
     */
    public function getIpAddress(): ?string {
        return $this->ip_address ?? Utils::getIp();
    }

    /**
     * @param string $ip_address
     */
    public function setIpAddress(string $ip_address): void {
        $this->ip_address = $ip_address;
    }

    /**
     * @return string|null
     */
    public function getUserAgent(): ?string {
        return $this->user_agent ?? Utils::getUserAgent();
    }

    /**
     * @param string|null $user_agent
     */
    public function setUserAgent(?string $user_agent): void {
        $this->user_agent = $user_agent;
    }

    /**
     * @return bool
     */
    public function getAcceptsMarketing(): bool {
        return $this->accepts_marketing;
    }

    /**
     * @param bool $accepts_marketing
     */
    public function setAcceptsMarketing(bool $accepts_marketing): void {
        $this->accepts_marketing = $accepts_marketing;
    }

    /**
     * @return bool
     */
    public function isValid(): bool {
        $validator = new Validator;
        $validation = $validator->validate(array(
            'phone' => $this->getPhone(),
        ), [
            'phone' => 'required',
        ]);

        return !$validation->fails();
    }

    public function __toString() {
        return $this->getPhone();
    }
}