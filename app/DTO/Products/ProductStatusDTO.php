<?php

namespace App\DTO\Products;

class ProductStatusDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly int $productId,
        public readonly ?string $uniqueCode = null,
    ) {}
}