<?php

namespace App\DTO\Products;

class ProductDTO
{
    public function __construct(
        public ?string $name,
        public ?string $description,
        public ?int $purchase_price,
        public ?int $rent_price_per_hour,
    )
    {
        $this->name = $name;
        $this->description = $description;
        $this->purchase_price = $purchase_price;
        $this->rent_price_per_hour = $rent_price_per_hour;
    }
    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn($value) => !is_null($value));
    }
}