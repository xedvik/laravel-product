<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\DTO\Products\ProductDTO;
use App\DTO\Products\ProductAuthorizationDTO;
use App\Http\Resources\ProductsResource;
use App\Services\ProductService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    use ApiResponse;
    private $productService;

    public function __construct(
        ProductService $productService,
    ) {
        $this->productService = $productService;
    }

    #[OA\Get(
        path: '/api/products',
        operationId: 'getProductsList',
        tags: ['Products'],
        summary: 'Получить список всех товаров',
        description: 'Возвращает список всех доступных товаров в системе. Требуется авторизация.',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список товаров получен успешно',
                content: new OA\JsonContent(ref: '#/components/schemas/ProductListResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Неавторизованный доступ',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            )
        ]
    )]
    public function index(): JsonResponse
    {
        $products = $this->productService->all();
        if ($products->isEmpty()) {
            return $this->successResponse([], 'Нет доступных товаров');
        }
        return $this->successResponse(ProductsResource::collection($products), 'Товары получены успешно');
    }

    #[OA\Post(
        path: '/api/products',
        operationId: 'storeProduct',
        tags: ['Products'],
        summary: 'Создать новый товар',
        description: 'Создает новый товар в системе. Требуется роль администратора.',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Данные для создания товара',
            content: new OA\JsonContent(ref: '#/components/schemas/StoreProductRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Товар создан успешно',
                content: new OA\JsonContent(ref: '#/components/schemas/ProductResponse')
            ),
            new OA\Response(
                response: 400,
                description: 'Не удалось создать товар',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Неавторизованный доступ',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 403,
                description: 'Недостаточно прав доступа',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 422,
                description: 'Ошибка валидации',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
            )
        ]
    )]
    public function store(StoreProductRequest $request): JsonResponse
    {
        $dto = new ProductDTO(
            name: $request->name,
            description: $request->description,
            purchase_price: $request->purchase_price,
            rent_price_per_hour: $request->rent_price_per_hour,
        );

        $product = $this->productService->create($dto);
        if (!$product) {
            return $this->errorResponse('Не удалось создать товар', 400);
        }

        return $this->successResponse(new ProductsResource($product), 'Товар создан успешно', 201);
    }

    #[OA\Get(
        path: '/api/products/{id}',
        operationId: 'getProduct',
        tags: ['Products'],
        summary: 'Получить товар по ID',
        description: 'Возвращает данные конкретного товара по его идентификатору. Требуется авторизация.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'Идентификатор товара',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Товар получен успешно',
                content: new OA\JsonContent(ref: '#/components/schemas/ProductResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Неавторизованный доступ',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 404,
                description: 'Товар не найден',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            )
        ]
    )]
    public function show($id)
    {
        $product = $this->productService->findByid($id);
        if (!$product) {
            return $this->errorResponse('Товар не найден', 404);
        }

        return $this->successResponse(new ProductsResource($product), 'Товар получен успешно');
    }

    #[OA\Put(
        path: '/api/products/{id}',
        operationId: 'updateProduct',
        tags: ['Products'],
        summary: 'Обновить товар',
        description: 'Обновляет данные существующего товара. Требуется роль администратора.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'Идентификатор товара',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Данные для обновления товара',
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateProductRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Товар обновлен успешно',
                content: new OA\JsonContent(ref: '#/components/schemas/ProductResponse')
            ),
            new OA\Response(
                response: 400,
                description: 'Не удалось обновить товар',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Неавторизованный доступ',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 403,
                description: 'Недостаточно прав доступа',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 404,
                description: 'Товар не найден',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 422,
                description: 'Ошибка валидации',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
            )
        ]
    )]
    public function update(UpdateProductRequest $request, $id): JsonResponse
    {
        $dto = new ProductDTO(
            name: $request->name,
            description: $request->description,
            purchase_price: $request->purchase_price,
            rent_price_per_hour: $request->rent_price_per_hour,
        );

        $product = $this->productService->update($dto, $id);
        if (!$product) {
            return $this->errorResponse('Не удалось обновить товар');
        }

        return $this->successResponse(new ProductsResource($product), 'Товар обновлен успешно');
    }

    #[OA\Delete(
        path: '/api/products/{id}',
        operationId: 'deleteProduct',
        tags: ['Products'],
        summary: 'Удалить товар',
        description: 'Удаляет товар из системы. Требуется роль администратора.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'Идентификатор товара',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Товар удален успешно',
                content: new OA\JsonContent(ref: '#/components/schemas/SuccessResponse')
            ),
            new OA\Response(
                response: 400,
                description: 'Не удалось удалить товар',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Неавторизованный доступ',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 403,
                description: 'Недостаточно прав доступа',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 404,
                description: 'Товар не найден',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            )
        ]
    )]
    public function destroy($id): JsonResponse
    {
        $this->authorize('delete', new ProductAuthorizationDTO());
        $product = $this->productService->delete($id);
        if (!$product) {
            return $this->errorResponse('Не удалось удалить товар');
        }

        return $this->successResponse(null, 'Товар удален успешно');
    }
}
