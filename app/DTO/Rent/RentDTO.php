<?php

namespace App\DTO\Rent;

use App\Enums\OwnershipType;

/**
 * DTO для аренды
 * @param int $product_id - ID товара
 * @param int $user_id - ID пользователя
 * @param int $hours - количество часов аренды
 * @param string $unique_code - уникальный код аренды
 */

class RentDTO
{
    public function __construct(
        public int $product_id,
        public int $user_id,
        public int $hours,
        public OwnershipType $type = OwnershipType::RENT,
        public ?string $unique_code = null,
    ) {}
}