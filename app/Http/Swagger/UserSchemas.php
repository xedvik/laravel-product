<?php

namespace App\Http\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "UserWithBalance",
    type: "object",
    title: "Пользователь с балансом",
    properties: [
        new OA\Property(
            property: "id",
            type: "integer",
            description: "Идентификатор пользователя",
            example: 1
        ),
        new OA\Property(
            property: "name",
            type: "string",
            description: "Имя пользователя",
            example: "Иван Иванов"
        ),
        new OA\Property(
            property: "email",
            type: "string",
            format: "email",
            description: "Email пользователя",
            example: "ivan@example.com"
        ),
        new OA\Property(
            property: "balance",
            type: "number",
            format: "integer",
            description: "Баланс пользователя",
            example: 1500.50
        ),
        new OA\Property(
            property: "created_at",
            type: "string",
            format: "date-time",
            description: "Дата регистрации",
            example: "2024-01-01T12:00:00Z"
        ),
        new OA\Property(
            property: "ownerships",
            type: "array",
            description: "Владения пользователя",
            items: new OA\Items(ref: "#/components/schemas/Ownership")
        )
    ]
)]

#[OA\Schema(
    schema: "UpdateBalanceRequest",
    type: "object",
    title: "Запрос на обновление баланса",
    required: ["amount"],
    properties: [
        new OA\Property(
            property: "amount",
            type: "number",
            format: "integer",
            description: "Сумма для изменения баланса (может быть отрицательной)",
            example: 100
        )
    ]
)]

#[OA\Schema(
    schema: "UserInfoResponse",
    type: "object",
    title: "Ответ с информацией о пользователе",
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
            example: "Информация о пользователе получена успешно"
        ),
        new OA\Property(
            property: "data",
            ref: "#/components/schemas/UserWithBalance",
            description: "Данные пользователя"
        )
    ]
)]

#[OA\Schema(
    schema: "UserBalanceResponse",
    type: "object",
    title: "Ответ с балансом пользователя",
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
            example: "Баланс пользователя получен успешно"
        ),
        new OA\Property(
            property: "data",
            type: "object",
            description: "Данные баланса пользователя",
            properties: [
                new OA\Property(
                    property: "id",
                    type: "integer",
                    description: "Идентификатор пользователя",
                    example: 1
                ),
                new OA\Property(
                    property: "balance",
                    type: "number",
                    format: "integer",
                    description: "Баланс пользователя",
                    example: 1500
                )
            ]
        )
    ]
)]

class UserSchemas
{
    // Схемы для UserController
}
