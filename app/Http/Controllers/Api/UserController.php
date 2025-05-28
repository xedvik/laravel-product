<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    use ApiResponse;
    private $userService;
    public function __construct(
        UserService $userService,
    ) {
        $this->userService = $userService;
    }

    #[OA\Get(
        path: '/api/user/info',
        operationId: 'getUserInfo',
        tags: ['User'],
        summary: 'Получить информацию о пользователе',
        description: 'Возвращает полную информацию о текущем авторизованном пользователе, включая его владения товарами. Требуется авторизация.',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Информация о пользователе получена успешно',
                content: new OA\JsonContent(ref: '#/components/schemas/UserInfoResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Неавторизованный доступ',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            )
        ]
    )]
    public function getUserInfo(Request $request): JsonResponse
    {
        $user = $request->user()->load('ownerships');
        return $this->successResponse(new UserResource($user), 'Информация о пользователе получена успешно');
    }

    #[OA\Get(
        path: '/api/user/balance',
        operationId: 'getUserBalance',
        tags: ['User'],
        summary: 'Получить баланс пользователя',
        description: 'Возвращает текущий баланс авторизованного пользователя. Требуется авторизация.',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Баланс пользователя получен успешно',
                content: new OA\JsonContent(ref: '#/components/schemas/UserBalanceResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Неавторизованный доступ',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            )
        ]
    )]
    public function getUserBalance(Request $request): JsonResponse
    {
        return $this->successResponse(new UserResource($request->user(), 'balance'), 'Баланс пользователя получен успешно');
    }

    #[OA\Put(
        path: '/api/user/balance',
        operationId: 'updateUserBalance',
        tags: ['User'],
        summary: 'Обновить баланс пользователя',
        description: 'Обновляет баланс пользователя. Требуется авторизация и специальные права доступа.',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateBalanceRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Баланс пользователя обновлен успешно',
                content: new OA\JsonContent(ref: '#/components/schemas/UserBalanceResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Неавторизованный доступ',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 403,
                description: 'Недостаточно прав для выполнения операции',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 422,
                description: 'Ошибка валидации данных',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
            )
        ]
    )]
    public function updateUserBalance(UserRequest $request): JsonResponse
    {
        $this->checkAccess('updateUserBalance', $request->user());
        $this->userService->updateUserBalance($request->user(), $request->amount);
        return $this->successResponse(new UserResource($request->user(), 'balance'), 'Баланс пользователя обновлен успешно');
    }
}
