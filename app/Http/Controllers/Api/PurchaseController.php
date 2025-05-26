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

class PurchaseController extends Controller
{
    use ApiResponse;

    public function __construct(
        private PurchaseService $purchaseService
    ) {}

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
