<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;
    private $userService;
    public function __construct(
        UserService $userService,
    ) {
        $this->userService = $userService;
    }
    public function getUserInfo(Request $request): JsonResponse
    {
        $user = $request->user()->load('ownerships');
        return $this->successResponse(new UserResource($user), 'Информация о пользователе получена успешно');
    }
    public function getUserBalance(Request $request): JsonResponse
    {
        return $this->successResponse(new UserResource($request->user(), 'balance'), 'Баланс пользователя получен успешно');
    }
    public function updateUserBalance(UserRequest $request): JsonResponse
    {
        $this->checkAccess('updateUserBalance', $request->user());
        $this->userService->updateUserBalance($request->user(), $request->amount);
        return $this->successResponse(new UserResource($request->user(), 'balance'), 'Баланс пользователя обновлен успешно');
    }
}