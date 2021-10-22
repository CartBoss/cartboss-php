<?php

namespace CartBoss\Api\Resources;

use CartBoss\Api\Interfaces\PayloadInterface;
use Rakit\Validation\Validator;

class Coupon implements PayloadInterface
{
    const CODE = 'code';
    const TYPE = 'type';
    const VALUE = 'value';

    const TYPE_FIXED_AMOUNT = 'FIXED_AMOUNT';
    const TYPE_PERCENTAGE = 'PERCENTAGE';
    const TYPE_FREE_SHIPPING = 'FREE_SHIPPING';
    const TYPE_CUSTOM = 'CUSTOM';

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
        $this->code = trim($code);
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
        $this->type = trim(strtoupper($type));
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
        $this->value = trim($value);
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
    public function isCustom(): bool
    {
        return $this->type == self::TYPE_CUSTOM;
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

    public function isValid(): bool
    {
        $validator = new Validator;
        $validation = $validator->validate(array(
            'code' => $this->code,
            'type' => $this->type,
            'value' => $this->value,
        ), [
            'code' => 'required',
            'type' => "required|in:FIXED_AMOUNT,PERCENTAGE,FREE_SHIPPING,CUSTOM",
        ]);

        return !$validation->fails();
    }

    public function getPayload(): array
    {
        return array(
            self::CODE => $this->getCode(),
            self::TYPE => $this->getType(),
            self::VALUE => $this->getValue(),
        );
    }

    public function __toString()
    {
        return $this->code;
    }
}