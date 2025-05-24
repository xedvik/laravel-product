<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\AuthRequest;
use App\DTO\Auth\RegisterDTO;
use App\DTO\Auth\LoginDTO;
use App\Traits\ApiResponse;
use App\Services\UserService;
use \Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class AuthController extends Controller
{
    use ApiResponse;
    private $userService;
    public function __construct(
        UserService $userService,
    ) {
        $this->userService = $userService;
    }
    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = new RegisterDTO(
            name: $request->name,
            email: $request->email,
            password: $request->password,
        );
        $user = $this->userService->create($dto);
        if (!$user) {
            return $this->errorResponse('User creation failed');
        }
        return $this->successResponse($user, 'User created successfully',201);
    }

    public function login(AuthRequest $request): JsonResponse
    {
        $dto = new LoginDTO(
            email: $request->email,
            password: $request->password,
        );
        $user = $this->userService->login($dto);
        if (!$user) {
            return $this->errorResponse('Invalid credentials');
        }
        return $this->successResponse($user, 'Login successful');
    }
    public function logout(Request $request): JsonResponse{
        $this->userService->logout($request->user());
        return $this->successResponse(null, 'Logout successful');
    }
}
