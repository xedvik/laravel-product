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
class AuthController extends Controller
{
    use ApiResponse;
    private $authService;
    public function __construct(
        AuthService $authService,
    ) {
        $this->authService = $authService;
    }
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
    public function logout(Request $request): JsonResponse{
        $this->authService->logout($request->user());
        return $this->successResponse(null, 'Logout successful');
    }
}
