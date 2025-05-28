<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PurchaseService;
use App\DTO\PurchaseDTO;
use App\Http\Requests\PurchaseRequest;
use App\Http\Resources\OwnershipResource;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\ProductAlreadyOwnedException;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\UserNotFoundException;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Enums\OwnershipType;
use OpenApi\Attributes as OA;

class PurchaseController extends Controller
{
    use ApiResponse;

    public function __construct(
        private PurchaseService $purchaseService
    ) {}

    #[OA\Post(
        path: '/api/purchase',
        operationId: 'purchaseProduct',
        tags: ['Purchase'],
        summary: 'Купить товар',
        description: 'Позволяет пользователю купить товар. После покупки товар становится собственностью пользователя навсегда. Требуется авторизация.',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PurchaseRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Товар успешно куплен',
                content: new OA\JsonContent(ref: '#/components/schemas/OwnershipResponse')
            ),
            new OA\Response(
                response: 400,
                description: 'Ошибка валидации или бизнес-логики (недостаточно средств, товар уже куплен)',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Неавторизованный доступ',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 404,
                description: 'Товар или пользователь не найден',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 500,
                description: 'Внутренняя ошибка сервера',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            )
        ]
    )]
    public function purchase(PurchaseRequest $request): JsonResponse
    {
        try {
            $dto = new PurchaseDTO(
                product_id: $request->validated('product_id'),
                user_id: $request->user()->id,
                unique_code: $request->validated('unique_code'),
                type: OwnershipType::PURCHASE
            );

            $ownership = $this->purchaseService->purchaseProduct($dto);

            return $this->resourceResponse(
                new OwnershipResource($ownership),
                'Товар успешно куплен',
                201
            );

        } catch (ProductNotFoundException|UserNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);

        } catch (InsufficientBalanceException|ProductAlreadyOwnedException $e) {
            return $this->errorResponse($e->getMessage(), 400);

        } catch (\Exception $e) {
            $message = 'Произошла ошибка при покупке товара';
            $errors = null;
            if ($this->checkAccess('view-detailed-errors', $request->user())) {
                $errors = [
                    'exception' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => config('app.debug') ? $e->getTraceAsString() : null,
                ];
            }
            return $this->errorResponse($message, 500, $errors);
        }
    }
}
