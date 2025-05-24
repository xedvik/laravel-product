<?php

namespace App\Services;
use App\DTO\Products\ProductDTO;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Product;
class ProductService
{
    private $productRepository;
    public function __construct(
        ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
    }
    public function all(): Collection
    {
        return $this->productRepository->all();
    }
    public function create(ProductDTO $dto): Product
    {
        return $this->productRepository->create($dto);
    }
    public function update(ProductDTO $dto, int $id): Product|null
    {
        $product = $this->findById($id);
        if(!$product){
            return null;
        }
        return $this->productRepository->update($dto, $product);
    }
    public function delete(int $id): bool
    {
        $product = $this->findById($id);
        if(!$product){
            return false;
        }
        return $this->productRepository->delete($product);
    }
    public function findById(int $id): Product|null
    {
        return $this->productRepository->findById($id);
    }
}