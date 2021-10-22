<?php

namespace CartBoss\Api\Resources\Addresses;

class BillingAddress extends BaseAddress
{
    const PHONE = 'phone';
    const EMAIL = 'email';

    /**
     * @var string|null
     */
    private $phone;
    /**
     * @var string|null
     */
    private $email;

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return !empty($this->phone) && strlen($this->phone) > 5;
    }

    public function getPayload(): array
    {
        return array_merge(parent::getPayload(), array(
            self::PHONE => $this->getPhone(),
            self::EMAIL => $this->getEmail(),
        ));
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}