<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\DTO\Products\ProductStatusDTO;
use App\Http\Requests\ProductStatusRequest;
use App\Http\Resources\ProductStatusResource;
use App\Services\ProductStatusService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProductStatusController extends Controller
{
    use ApiResponse;

    public function __construct(
        private ProductStatusService $productStatusService
    ) {
    }

    #[OA\Get(
        path: '/api/products/{productId}/status',
        operationId: 'checkUserProductStatus',
        tags: ['Product Status'],
        summary: 'Проверить статус товара для пользователя',
        description: 'Проверяет статус конкретного товара для текущего авторизованного пользователя. Возвращает информацию о владении товаром.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'productId',
                in: 'path',
                description: 'Идентификатор товара',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Статус товара получен успешно',
                content: new OA\JsonContent(ref: '#/components/schemas/ProductStatusResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Неавторизованный доступ',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 404,
                description: 'Товар не найден в ваших владениях',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            )
        ]
    )]
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

    #[OA\Get(
        path: '/api/products/status/{uniqueCode}',
        operationId: 'checkStatusByUniqueCode',
        tags: ['Product Status'],
        summary: 'Проверить статус товара по уникальному коду',
        description: 'Проверяет статус товара по его уникальному коду. Не требует авторизации.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'uniqueCode',
                in: 'path',
                description: 'Уникальный код товара',
                required: true,
                schema: new OA\Schema(type: 'string', example: 'PROD-001')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Статус товара получен успешно',
                content: new OA\JsonContent(ref: '#/components/schemas/ProductStatusResponse')
            ),
            new OA\Response(
                response: 404,
                description: 'Товар с указанным кодом не найден',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            )
        ]
    )]
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

    #[OA\Get(
        path: '/api/user/ownerships',
        operationId: 'getUserOwnerships',
        tags: ['Product Status'],
        summary: 'Получить все владения пользователя',
        description: 'Возвращает список всех товаров, которыми владеет текущий пользователь (купленные и арендованные). Требуется авторизация.',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список владений получен успешно',
                content: new OA\JsonContent(ref: '#/components/schemas/UserOwnershipsResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Неавторизованный доступ',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            )
        ]
    )]
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
