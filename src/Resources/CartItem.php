<?php

namespace CartBoss\Api\Resources;

class CartItem {
    /**
     * @var string|null
     */
    private $id;
    /**
     * @var string|null
     */
    private $variation_id;
    /**
     * @var string|null
     */
    private $name;
    /**
     * @var int
     */
    private $quantity = 1;
    /**
     * @var string|null
     */
    private $image_url;
    /**
     * @var float
     */
    private $price = 0.0;

    /**
     * @return string|null
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * @param string|null $id
     */
    public function setId(?string $id): void {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getVariationId(): ?string {
        return $this->variation_id;
    }

    /**
     * @param string|null $variation_id
     */
    public function setVariationId(?string $variation_id): void {
        $this->variation_id = $variation_id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getQuantity(): int {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void {
        $this->quantity = $quantity;
    }

    /**
     * @return string|null
     */
    public function getImageUrl(): ?string {
        return $this->image_url;
    }

    /**
     * @param string|null $image_url
     */
    public function setImageUrl(?string $image_url): void {
        $this->image_url = $image_url;
    }

    /**
     * @return float
     */
    public function getPrice(): float {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void {
        $this->price = $price;
    }
}



