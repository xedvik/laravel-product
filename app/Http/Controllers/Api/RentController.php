<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RentRequest;
use App\Http\Requests\ExtendRentRequest;
use App\Services\RentService;
use App\DTO\Rent\RentDTO;
use App\DTO\Rent\ExtendRentDTO;
use App\Enums\OwnershipType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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

class RentController extends Controller
{
    use ApiResponse;

    public function __construct(
        private RentService $rentService
    ) {}

    public function rent(RentRequest $request)
    {
        try {
            $dto = new RentDTO(
                product_id: $request->validated('product_id'),
                user_id: Auth::id(),
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
            if (Gate::allows('view-detailed-errors')) {
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
            if (Gate::allows('view-detailed-errors')) {
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