<?php

namespace App\Http\Swagger;

use OpenApi\Attributes as OA;

#[
    OA\Info(
        version: "1.0.0",
        title: "Laravel Product API",
        description: "API для управления товарами, арендой и покупками в системе Laravel Product",
    ),
    OA\Server(url: 'http://localhost:8000', description: "Локальный сервер разработки"),
    OA\SecurityScheme(
        securityScheme: 'sanctum',
        type: "http",
        scheme: "bearer",
        bearerFormat: "JWT",
        description: "Аутентификация через Laravel Sanctum токены"
    ),
    OA\Tag(name: "Authentication", description: "API для аутентификации пользователей"),
    OA\Tag(name: "Products", description: "API для управления товарами"),
    OA\Tag(name: "Rent", description: "API для управления арендой товаров"),
    OA\Tag(name: "Purchase", description: "API для покупки товаров"),
    OA\Tag(name: "Product Status", description: "API для проверки статуса товаров"),
    OA\Tag(name: "User", description: "API для управления пользователями")
]

// Общие схемы ответов
#[OA\Schema(
    schema: "ErrorResponse",
    type: "object",
    title: "Ответ с ошибкой",
    description: "Стандартный ответ API при ошибке",
    properties: [
        new OA\Property(property: "success", type: "boolean", description: "Статус успешности операции", example: false),
        new OA\Property(property: "message", type: "string", description: "Сообщение об ошибке", example: "Товар не найден"),
        new OA\Property(property: "errors", type: "object", nullable: true, description: "Детали ошибок (опционально)")
    ]
)]

#[OA\Schema(
    schema: "ValidationErrorResponse",
    type: "object",
    title: "Ответ с ошибками валидации",
    description: "Ответ API при ошибках валидации",
    properties: [
        new OA\Property(property: "success", type: "boolean", description: "Статус успешности операции", example: false),
        new OA\Property(property: "message", type: "string", description: "Сообщение об ошибке", example: "Ошибка валидации данных"),
        new OA\Property(
            property: "errors",
            type: "object",
            description: "Детали ошибок валидации",
            properties: [
                new OA\Property(property: "name", type: "array", items: new OA\Items(type: "string"), example: ["Имя обязательно"]),
                new OA\Property(property: "purchase_price", type: "array", items: new OA\Items(type: "string"), example: ["Цена покупки должна быть числом"])
            ]
        )
    ]
)]

#[OA\Schema(
    schema: "SuccessResponse",
    type: "object",
    title: "Успешный ответ",
    description: "Стандартный успешный ответ API",
    properties: [
        new OA\Property(property: "success", type: "boolean", description: "Статус успешности операции", example: true),
        new OA\Property(property: "message", type: "string", description: "Сообщение о результате операции", example: "Операция выполнена успешно"),
        new OA\Property(property: "data", nullable: true, description: "Данные ответа (если необходимо)")
    ]
)]

class ApiInfo
{
    // Класс содержит основную информацию об API и общие схемы
}