<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\DTO\Products\ProductDTO;
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
        if (!$products) {
            return $this->errorResponse('Products not found', 404);
        }
        return $this->successResponse($products, 'Products fetched successfully');
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
            return $this->errorResponse('Product not created', 400);
        }
        return $this->successResponse($product, 'Product created successfully',201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = $this->productService->findByid($id);
        if(!$product){
            return $this->errorResponse('Product not found', 404);
        }
        return $this->successResponse($product, 'Product fetched successfully');
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
            return $this->errorResponse('Product not updated');
        }
        return $this->successResponse($product, 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $product = $this->productService->delete($id);
        if(!$product){
            return $this->errorResponse('Product not deleted');
        }
        return $this->successResponse(null, 'Product deleted successfully',204);
    }
}
