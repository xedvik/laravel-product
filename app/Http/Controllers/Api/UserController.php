<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use ApiResponse;
    private $userService;
    public function __construct(
        UserService $userService,
    ) {
        $this->userService = $userService;
    }
    public function getUserInfo(UserRequest $request): JsonResponse
    {
        $userInfo = $this->userService->getUserInfo($request->user());
        return $this->successResponse(new UserResource($userInfo), 'Информация о пользователе получена успешно');
    }
    public function getUserBalance(UserRequest $request): JsonResponse
    {
        $this->checkAccess('getUserBalance', $request->user());
        $user = $this->userService->getUserBalance($request->user());
        return $this->successResponse(new UserResource($user, 'balance'), 'Баланс пользователя получен успешно');
    }
    public function updateUserBalance(UserRequest $request): JsonResponse
    {
        $this->checkAccess('updateUserBalance', $request->user());
        $user = $this->userService->updateUserBalance($request->user(), $request->amount);
        return $this->successResponse(new UserResource($user, 'balance'), 'Баланс пользователя обновлен успешно');
    }
}