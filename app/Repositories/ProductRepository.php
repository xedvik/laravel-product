<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\DTO\Products\ProductDTO;

class ProductRepository implements ProductRepositoryInterface
{
    public function all(): Collection
    {
        return Product::all();
    }
    public function create(ProductDTO $dto): Product
    {
        $product = Product::create([
            'name' => $dto->name,
            'description' => $dto->description,
            'purchase_price' => $dto->purchase_price,
            'rent_price_per_hour' => $dto->rent_price_per_hour,
        ]);
        return $product;
    }
    public function update(ProductDTO $dto, Product $product): Product
    {
        $product->update($dto->toArray());
        return $product;
    }
    public function delete(Product $product): bool
    {
        return $product->delete();
    }
    public function findById(int $id): Product|null
    {
        return Product::findOrFail($id);
    }
}