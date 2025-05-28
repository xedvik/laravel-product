<?php

namespace App\Http\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "RentRequest",
    type: "object",
    title: "Запрос на аренду товара",
    required: ["product_id", "unique_code", "hours"],
    properties: [
        new OA\Property(
            property: "product_id",
            type: "integer",
            description: "Идентификатор товара для аренды",
            example: 1
        ),
        new OA\Property(
            property: "unique_code",
            type: "string",
            description: "Уникальный код товара",
            example: "PROD-001"
        ),
        new OA\Property(
            property: "hours",
            type: "integer",
            description: "Количество часов аренды",
            minimum: 1,
            example: 24
        )
    ]
)]

#[OA\Schema(
    schema: "ExtendRentRequest",
    type: "object",
    title: "Запрос на продление аренды",
    required: ["ownership_id", "additional_hours"],
    properties: [
        new OA\Property(
            property: "ownership_id",
            type: "integer",
            description: "Идентификатор аренды",
            example: 1
        ),
        new OA\Property(
            property: "additional_hours",
            type: "integer",
            description: "Дополнительные часы аренды",
            minimum: 1,
            example: 12
        )
    ]
)]

#[OA\Schema(
    schema: "Ownership",
    type: "object",
    title: "Владение товаром",
    properties: [
        new OA\Property(
            property: "id",
            type: "integer",
            description: "Идентификатор владения",
            example: 1
        ),
        new OA\Property(
            property: "product_id",
            type: "integer",
            description: "Идентификатор товара",
            example: 1
        ),
        new OA\Property(
            property: "user_id",
            type: "integer",
            description: "Идентификатор пользователя",
            example: 1
        ),
        new OA\Property(
            property: "unique_code",
            type: "string",
            description: "Уникальный код товара",
            example: "PROD-001"
        ),
        new OA\Property(
            property: "type",
            type: "string",
            description: "Тип владения",
            enum: ["rent", "purchase"],
            example: "rent"
        ),
        new OA\Property(
            property: "expires_at",
            type: "string",
            format: "date-time",
            description: "Дата окончания аренды (только для аренды)",
            example: "2024-01-15T12:00:00Z"
        ),
        new OA\Property(
            property: "created_at",
            type: "string",
            format: "date-time",
            description: "Дата создания",
            example: "2024-01-01T12:00:00Z"
        ),
        new OA\Property(
            property: "updated_at",
            type: "string",
            format: "date-time",
            description: "Дата последнего обновления",
            example: "2024-01-01T12:00:00Z"
        )
    ]
)]

#[OA\Schema(
    schema: "OwnershipResponse",
    type: "object",
    title: "Ответ с данными владения",
    properties: [
        new OA\Property(
            property: "success",
            type: "boolean",
            description: "Статус успешности операции",
            example: true
        ),
        new OA\Property(
            property: "message",
            type: "string",
            description: "Сообщение о результате операции",
            example: "Товар успешно арендован"
        ),
        new OA\Property(
            property: "data",
            ref: "#/components/schemas/Ownership",
            description: "Данные владения"
        )
    ]
)]

class RentSchemas
{
    // Схемы для RentController
}
