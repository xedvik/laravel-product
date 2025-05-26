<?php

namespace App\DTO\Products;

/**
 * DTO для товара
 * @param string $name - название товара
 * @param string $description - описание товара
 * @param int $purchase_price - цена покупки
 * @param int $rent_price_per_hour - цена аренды за час
 */
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
    //Получение массива данных без null
    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn($value) => !is_null($value));
    }
}