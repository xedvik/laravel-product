<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RentRequest;
use App\Http\Requests\ExtendRentRequest;
use App\Services\RentService;
use App\DTO\Rent\RentDTO;
use App\DTO\Rent\ExtendRentDTO;
use App\Enums\OwnershipType;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\ProductAlreadyOwnedException;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\ProductNotAvailableException;
use App\Exceptions\OwnershipNotFoundException;
use App\Exceptions\InvalidRentDurationException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\RentExpiredException;
use App\Traits\ApiResponse;
use App\Http\Resources\OwnershipResource;
use OpenApi\Attributes as OA;

class RentController extends Controller
{
    use ApiResponse;

    public function __construct(
        private RentService $rentService
    ) {}

    #[OA\Post(
        path: '/api/rent',
        operationId: 'rentProduct',
        tags: ['Rent'],
        summary: 'Арендовать товар',
        description: 'Позволяет пользователю арендовать товар на определенное количество часов. Требуется авторизация.',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/RentRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Товар успешно арендован',
                content: new OA\JsonContent(ref: '#/components/schemas/OwnershipResponse')
            ),
            new OA\Response(
                response: 400,
                description: 'Ошибка валидации или бизнес-логики',
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
    public function rent(RentRequest $request)
    {
        try {
            $dto = new RentDTO(
                product_id: $request->validated('product_id'),
                user_id: $request->user()->id,
                unique_code: $request->validated('unique_code'),
                type: OwnershipType::RENT,
                hours: $request->validated('hours')
            );

            $ownership = $this->rentService->rentProduct($dto);

            return $this->resourceResponse(
                new OwnershipResource($ownership),
                'Товар успешно арендован',
                201
            );

        } catch (ProductNotFoundException|UserNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);

        } catch (InsufficientBalanceException|InvalidRentDurationException|ProductNotAvailableException|ProductAlreadyOwnedException $e) {
            return $this->errorResponse($e->getMessage(), 400);

        } catch (\Exception $e) {
            $message = 'Произошла ошибка при аренде товара';
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

    #[OA\Post(
        path: '/api/rent/extend',
        operationId: 'extendRent',
        tags: ['Rent'],
        summary: 'Продлить аренду товара',
        description: 'Позволяет пользователю продлить существующую аренду товара на дополнительные часы. Требуется авторизация.',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ExtendRentRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Аренда успешно продлена',
                content: new OA\JsonContent(ref: '#/components/schemas/OwnershipResponse')
            ),
            new OA\Response(
                response: 400,
                description: 'Ошибка валидации или бизнес-логики (аренда истекла, недостаточно средств)',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Неавторизованный доступ',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 404,
                description: 'Аренда, товар или пользователь не найден',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 500,
                description: 'Внутренняя ошибка сервера',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            )
        ]
    )]
    public function extendRent(ExtendRentRequest $request)
    {
        try {
            $dto = new ExtendRentDTO(
                ownership_id: $request->validated('ownership_id'),
                additional_hours: $request->validated('additional_hours')
            );

            $ownership = $this->rentService->extendRent($dto);

            return $this->resourceResponse(
                new OwnershipResource($ownership),
                'Аренда успешно продлена',
                200
            );
        } catch(OwnershipNotFoundException|ProductNotFoundException|UserNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);

        } catch(RentExpiredException|InsufficientBalanceException|InvalidRentDurationException $e) {
            return $this->errorResponse($e->getMessage(), 400);

        } catch(\Exception $e) {
            $message = 'Произошла ошибка при продлении аренды';
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
