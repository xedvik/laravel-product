<?php

namespace App\DTO;

use App\Enums\OwnershipType;

/**
 * DTO для покупки
 * @param int $product_id - ID товара
 * @param int $user_id - ID пользователя
 * @param OwnershipType $type - тип владения
 * @param string $unique_code - уникальный код владения
 */
class PurchaseDTO
{
    public function __construct(
        public int $product_id,
        public int $user_id,
        public OwnershipType $type = OwnershipType::PURCHASE,
        public ?string $unique_code = null,
    )
    {
    }
}
