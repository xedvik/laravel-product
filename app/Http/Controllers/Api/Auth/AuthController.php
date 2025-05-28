<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\AuthRequest;
use App\DTO\Auth\RegisterDTO;
use App\DTO\Auth\LoginDTO;
use App\Traits\ApiResponse;
use App\Services\AuthService;
use \Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    use ApiResponse;
    private $authService;
    public function __construct(
        AuthService $authService,
    ) {
        $this->authService = $authService;
    }

    #[OA\Post(
        path: '/api/register',
        operationId: 'registerUser',
        tags: ['Authentication'],
        summary: 'Регистрация нового пользователя',
        description: 'Создает нового пользователя в системе',
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Данные для регистрации пользователя',
            content: new OA\JsonContent(ref: '#/components/schemas/RegisterRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Пользователь успешно зарегистрирован',
                content: new OA\JsonContent(ref: '#/components/schemas/AuthResponse')
            ),
            new OA\Response(
                response: 422,
                description: 'Ошибка валидации или создания пользователя',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
            )
        ]
    )]
    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = new RegisterDTO(
            name: $request->name,
            email: $request->email,
            password: $request->password,
        );
        $user = $this->authService->create($dto);
        if (!$user) {
            return $this->errorResponse('User creation failed',422);
        }
        return $this->successResponse($user, 'User created successfully',201);
    }

    #[OA\Post(
        path: '/api/login',
        operationId: 'loginUser',
        tags: ['Authentication'],
        summary: 'Авторизация пользователя',
        description: 'Выполняет вход пользователя в систему и возвращает токен доступа',
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Данные для входа в систему',
            content: new OA\JsonContent(ref: '#/components/schemas/LoginRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Успешная авторизация',
                content: new OA\JsonContent(ref: '#/components/schemas/AuthResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Неверные учетные данные',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 422,
                description: 'Ошибка валидации',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
            )
        ]
    )]
    public function login(AuthRequest $request): JsonResponse
    {
        $dto = new LoginDTO(
            email: $request->email,
            password: $request->password,
        );
        $user = $this->authService->login($dto);
        if (!$user) {
            return $this->errorResponse('Invalid credentials', 401);
        }
        return $this->successResponse($user, 'Login successful');
    }

    #[OA\Post(
        path: '/api/logout',
        operationId: 'logoutUser',
        tags: ['Authentication'],
        summary: 'Выход из системы',
        description: 'Выполняет выход пользователя из системы и аннулирует токен',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Успешный выход из системы',
                content: new OA\JsonContent(ref: '#/components/schemas/SuccessResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Неавторизованный доступ',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            )
        ]
    )]
    public function logout(Request $request): JsonResponse{
        $this->authService->logout($request->user());
        return $this->successResponse(null, 'Logout successful');
    }
}
