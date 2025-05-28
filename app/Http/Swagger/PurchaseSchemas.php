<?php

namespace App\Http\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "PurchaseRequest",
    type: "object",
    title: "Запрос на покупку товара",
    required: ["product_id", "unique_code"],
    properties: [
        new OA\Property(
            property: "product_id",
            type: "integer",
            description: "Идентификатор товара для покупки",
            example: 1
        ),
        new OA\Property(
            property: "unique_code",
            type: "string",
            description: "Уникальный код товара",
            example: "PROD-001"
        )
    ]
)]

class PurchaseSchemas
{
    // Схемы для PurchaseController
}
