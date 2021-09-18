<?php

namespace CartBoss\Api\Resources;

class Coupon
{
    const TYPE_FIXED_AMOUNT = 'FIXED_AMOUNT';
    const TYPE_PERCENTAGE = 'PERCENTAGE';
    const TYPE_FREE_SHIPPING = 'FREE_SHIPPING';

    /**
     * @var string|null
     */
    private $code;
    /**
     * @var string|null
     */
    private $type;
    /**
     * @var string|null
     */
    private $value;

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string|null $value
     */
    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function isFixedAmount(): bool
    {
        return $this->type == self::TYPE_FIXED_AMOUNT;
    }

    /**
     * @return bool
     */
    public function isPercentage(): bool
    {
        return $this->type == self::TYPE_PERCENTAGE;
    }

    /**
     * @return bool
     */
    public function isFreeShipping(): bool
    {
        return $this->type == self::TYPE_FREE_SHIPPING;
    }
}