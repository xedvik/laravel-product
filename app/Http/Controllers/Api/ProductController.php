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
class ProductController extends Controller
{
    use ApiResponse;
    private $productService;
    public function __construct(
        ProductService $productService,
    ) {
        $this->productService = $productService;
    }

    public function index(): JsonResponse
    {
        $products = $this->productService->all();
        if ($products->isEmpty()) {
            return $this->successResponse([], 'Нет доступных товаров');
        }
        return $this->successResponse(ProductsResource::collection($products), 'Товары получены успешно');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {

        $dto = new ProductDTO(
            name: $request->name,
            description: $request->description,
            purchase_price: $request->purchase_price,
            rent_price_per_hour: $request->rent_price_per_hour,
        );
        $product = $this->productService->create($dto);
        if(!$product){
            return $this->errorResponse('Не удалось создать товар', 400);
        }
        return $this->successResponse(new ProductsResource( $product), 'Товар создан успешно',201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = $this->productService->findByid($id);
        if(!$product){
            return $this->errorResponse('Товар не найден', 404);
        }
        return $this->successResponse(new ProductsResource( $product), 'Товар получен успешно');
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, $id): JsonResponse
    {
        $dto = new ProductDTO(
            name: $request->name,
            description: $request->description,
            purchase_price: $request->purchase_price,
            rent_price_per_hour: $request->rent_price_per_hour,
        );
        $product = $this->productService->update($dto, $id);
        if(!$product){
            return $this->errorResponse('Не удалось обновить товар');
        }
        return $this->successResponse(new ProductsResource( $product), 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $this->authorize('delete', new ProductAuthorizationDTO());
        $product = $this->productService->delete($id);
        if(!$product){
            return $this->errorResponse('Не удалось удалить товар');
        }
        return $this->successResponse(null, 'Товар удален успешно');
    }
}
