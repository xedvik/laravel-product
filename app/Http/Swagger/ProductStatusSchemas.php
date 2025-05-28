<?php

namespace App\Http\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ProductStatus",
    type: "object",
    title: "Статус товара",
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
            property: "product_name",
            type: "string",
            description: "Название товара",
            example: "Ноутбук Dell XPS 13"
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
            property: "status",
            type: "string",
            description: "Статус владения",
            enum: ["active", "expired"],
            example: "active"
        ),
        new OA\Property(
            property: "expires_at",
            type: "string",
            format: "date-time",
            description: "Дата окончания аренды (только для аренды)",
            example: "2024-01-15T12:00:00Z",
            nullable: true
        ),
        new OA\Property(
            property: "created_at",
            type: "string",
            format: "date-time",
            description: "Дата создания",
            example: "2024-01-01T12:00:00Z"
        )
    ]
)]

#[OA\Schema(
    schema: "ProductStatusResponse",
    type: "object",
    title: "Ответ со статусом товара",
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
            example: "Статус товара получен успешно"
        ),
        new OA\Property(
            property: "data",
            ref: "#/components/schemas/ProductStatus",
            description: "Данные статуса товара"
        )
    ]
)]

#[OA\Schema(
    schema: "UserOwnershipsResponse",
    type: "object",
    title: "Ответ со списком владений пользователя",
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
            example: "Список ваших товаров получен успешно"
        ),
        new OA\Property(
            property: "data",
            type: "array",
            description: "Список владений пользователя",
            items: new OA\Items(ref: "#/components/schemas/ProductStatus")
        )
    ]
)]

class ProductStatusSchemas
{
    // Схемы для ProductStatusController
}
