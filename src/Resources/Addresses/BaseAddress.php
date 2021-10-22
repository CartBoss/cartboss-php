<?php

namespace CartBoss\Api\Resources\Addresses;

use CartBoss\Api\Interfaces\PayloadInterface;

abstract class BaseAddress implements PayloadInterface
{
    const FIRST_NAME = 'first_name';
    const LAST_NAME = 'last_name';
    const ADDRESS_1 = 'address_1';
    const ADDRESS_2 = 'address_2';
    const COMPANY = 'company';
    const CITY = 'city';
    const STATE = 'state';
    const POSTAL_CODE = 'postal_code';
    const COUNTRY = 'country';

    /**
     * @var string|null
     */
    private $first_name;
    /**
     * @var string|null
     */
    private $last_name;
    /**
     * @var string|null
     */
    private $company;
    /**
     * @var string|null
     */
    private $address_1;
    /**
     * @var string|null
     */
    private $address_2;
    /**
     * @var string|null
     */
    private $city;
    /**
     * @var string|null
     */
    private $state;
    /**
     * @var string|null
     */
    private $postal_code;
    /**
     * @var string|null
     */
    private $country;

    public function getPayload(): array
    {
        return array(
            self::FIRST_NAME => $this->getFirstName(),
            self::LAST_NAME => $this->getLastName(),
            self::ADDRESS_1 => $this->getAddress1(),
            self::ADDRESS_2 => $this->getAddress2(),
            self::COMPANY => $this->getCompany(),
            self::CITY => $this->getCity(),
            self::STATE => $this->getState(),
            self::POSTAL_CODE => $this->getPostalCode(),
            self::COUNTRY => $this->getCountry(),
        );
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    /**
     * @param string|null $first_name
     */
    public function setFirstName(?string $first_name): void
    {
        $this->first_name = $first_name;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    /**
     * @param string|null $last_name
     */
    public function setLastName(?string $last_name): void
    {
        $this->last_name = $last_name;
    }

    /**
     * @return string|null
     */
    public function getAddress1(): ?string
    {
        return $this->address_1;
    }

    /**
     * @param string|null $address_1
     */
    public function setAddress1(?string $address_1): void
    {
        $this->address_1 = $address_1;
    }

    /**
     * @return string|null
     */
    public function getAddress2(): ?string
    {
        return $this->address_2;
    }

    /**
     * @param string|null $address_2
     */
    public function setAddress2(?string $address_2): void
    {
        $this->address_2 = $address_2;
    }

    /**
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @param string|null $company
     */
    public function setCompany(?string $company): void
    {
        $this->company = $company;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param string|null $state
     */
    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return string|null
     */
    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    /**
     * @param string|null $postal_code
     */
    public function setPostalCode(?string $postal_code): void
    {
        $this->postal_code = $postal_code;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string|null $country
     */
    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

}

