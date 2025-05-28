<?php

namespace App\Http\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "RegisterRequest",
    type: "object",
    title: "Запрос регистрации",
    description: "Данные для регистрации нового пользователя",
    required: ["name", "email", "password"],
    properties: [
        new OA\Property(property: "name", type: "string", description: "Имя пользователя", example: "Иван Иванов"),
        new OA\Property(property: "email", type: "string", format: "email", description: "Email пользователя", example: "ivan@example.com"),
        new OA\Property(property: "password", type: "string", format: "password", minLength: 8, description: "Пароль пользователя", example: "password123")
    ]
)]

#[OA\Schema(
    schema: "LoginRequest",
    type: "object",
    title: "Запрос авторизации",
    description: "Данные для входа в систему",
    required: ["email", "password"],
    properties: [
        new OA\Property(property: "email", type: "string", format: "email", description: "Email пользователя", example: "ivan@example.com"),
        new OA\Property(property: "password", type: "string", format: "password", description: "Пароль пользователя", example: "password123")
    ]
)]

#[OA\Schema(
    schema: "User",
    type: "object",
    title: "Пользователь",
    description: "Модель пользователя",
    properties: [
        new OA\Property(property: "id", type: "integer", description: "Уникальный идентификатор пользователя", example: 1),
        new OA\Property(property: "name", type: "string", description: "Имя пользователя", example: "Иван Иванов"),
        new OA\Property(property: "email", type: "string", format: "email", description: "Email пользователя", example: "ivan@example.com"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", description: "Дата создания", example: "2024-01-15T10:30:00.000000Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", description: "Дата обновления", example: "2024-01-15T10:30:00.000000Z")
    ]
)]

#[OA\Schema(
    schema: "AuthResponse",
    type: "object",
    title: "Ответ аутентификации",
    description: "Ответ при успешной аутентификации с токеном и данными пользователя",
    properties: [
        new OA\Property(property: "success", type: "boolean", description: "Статус успешности операции", example: true),
        new OA\Property(property: "message", type: "string", description: "Сообщение о результате операции", example: "Login successful"),
        new OA\Property(
            property: "data",
            type: "object",
            properties: [
                new OA\Property(property: "user", ref: "#/components/schemas/User"),
                new OA\Property(property: "token", type: "string", description: "Токен доступа", example: "1|abcdef123456789...")
            ]
        )
    ]
)]

class AuthSchemas
{
    // Схемы для AuthController
}
