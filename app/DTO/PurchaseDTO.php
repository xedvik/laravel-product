<?php

namespace App\DTO;

use App\Enums\OwnershipType;

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
