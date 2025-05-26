<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\DTO\Products\ProductStatusDTO;
use App\Http\Requests\ProductStatusRequest;
use App\Http\Resources\ProductStatusResource;
use App\Services\ProductStatusService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ProductStatusController extends Controller
{
    use ApiResponse;

    public function __construct(
        private ProductStatusService $productStatusService
    ) {
    }

    /**
     * Проверяет статус товара для текущего пользователя
     */
    public function checkUserProductStatus(ProductStatusRequest $request, int $productId): JsonResponse
    {
        $dto = new ProductStatusDTO(
            userId: $request->user()->id,
            productId: $productId
        );

        $ownership = $this->productStatusService->checkUserProductStatus($dto);

        if (!$ownership) {
            return $this->errorResponse('Товар не найден в ваших владениях', 404);
        }

        return $this->successResponse(
            new ProductStatusResource($ownership),
            'Статус товара получен успешно'
        );
    }

    /**
     * Проверяет статус товара по уникальному коду
     */
    public function checkStatusByUniqueCode(string $uniqueCode): JsonResponse
    {
        $ownership = $this->productStatusService->checkStatusByUniqueCode($uniqueCode);

        if (!$ownership) {
            return $this->errorResponse('Товар с указанным кодом не найден', 404);
        }

        return $this->successResponse(
            new ProductStatusResource($ownership),
            'Статус товара получен успешно'
        );
    }

    /**
     * Получает все владения пользователя с уникальными кодами
     */
    public function getUserOwnerships(ProductStatusRequest $request): JsonResponse
    {
        $ownerships = $this->productStatusService->getUserOwnershipsWithCodes($request->user()->id);

        if (empty($ownerships)) {
            return $this->successResponse([], 'У вас нет товаров');
        }

        return $this->successResponse(
            $ownerships,
            'Список ваших товаров получен успешно'
        );
    }
}
