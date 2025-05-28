<?php

namespace App\Http\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Product",
    type: "object",
    title: "Товар",
    description: "Модель товара",
    required: ["id", "name", "purchase_price", "rent_price_per_hour", "created_at"],
    properties: [
        new OA\Property(property: "id", type: "integer", description: "Уникальный идентификатор товара", example: 1),
        new OA\Property(property: "name", type: "string", description: "Название товара", example: "Игровая консоль PlayStation 5"),
        new OA\Property(property: "description", type: "string", nullable: true, description: "Описание товара", example: "Новейшая игровая консоль от Sony с 4K поддержкой"),
        new OA\Property(property: "purchase_price", type: "integer", description: "Цена покупки товара", example: 50000),
        new OA\Property(property: "rent_price_per_hour", type: "integer", description: "Цена аренды товара за час", example: 500),
        new OA\Property(property: "created_at", type: "string", format: "date-time", description: "Дата создания", example: "2024-01-15T10:30:00.000000Z")
    ]
)]

#[OA\Schema(
    schema: "ProductRequest",
    type: "object",
    title: "Запрос для создания/обновления товара",
    description: "Данные для создания или обновления товара",
    properties: [
        new OA\Property(property: "name", type: "string", description: "Название товара", example: "Игровая консоль PlayStation 5"),
        new OA\Property(property: "description", type: "string", nullable: true, description: "Описание товара", example: "Новейшая игровая консоль от Sony с 4K поддержкой"),
        new OA\Property(property: "purchase_price", type: "number", format: "integer", minimum: 0, description: "Цена покупки товара", example: 50000),
        new OA\Property(property: "rent_price_per_hour", type: "number", format: "integer", minimum: 0, description: "Цена аренды товара за час", example: 500)
    ]
)]

#[OA\Schema(
    schema: "StoreProductRequest",
    allOf: [
        new OA\Schema(ref: "#/components/schemas/ProductRequest")
    ],
    required: ["name", "purchase_price", "rent_price_per_hour"]
)]

#[OA\Schema(
    schema: "UpdateProductRequest",
    allOf: [
        new OA\Schema(ref: "#/components/schemas/ProductRequest")
    ]
)]

#[OA\Schema(
    schema: "ProductResponse",
    type: "object",
    title: "Ответ с товаром",
    description: "Стандартный ответ API с данными товара",
    properties: [
        new OA\Property(property: "success", type: "boolean", description: "Статус успешности операции", example: true),
        new OA\Property(property: "message", type: "string", description: "Сообщение о результате операции", example: "Товар получен успешно"),
        new OA\Property(property: "data", ref: "#/components/schemas/Product")
    ]
)]

#[OA\Schema(
    schema: "ProductListResponse",
    type: "object",
    title: "Ответ со списком товаров",
    description: "Стандартный ответ API со списком товаров",
    properties: [
        new OA\Property(property: "success", type: "boolean", description: "Статус успешности операции", example: true),
        new OA\Property(property: "message", type: "string", description: "Сообщение о результате операции", example: "Товары получены успешно"),
        new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/Product"))
    ]
)]

class ProductSchemas
{
    // Схемы для ProductController
}
