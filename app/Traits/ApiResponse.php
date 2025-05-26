<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

trait ApiResponse
{
    protected function successResponse($data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function errorResponse(string $message = 'Error', int $status = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    protected function resourceResponse(JsonResource $resource, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $resource,
        ], $status);
    }
    protected function checkAccess(string $policy, User $user, string $message = 'У вас нет доступа к этой операции'): JsonResponse|bool
    {
        if (!Gate::allows($policy, $user)) {
            return $this->errorResponse($message, 403);
        }
        return true;
    }
}
