<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use App\DTO\Products\ProductDTO;
use App\Models\Product;
interface ProductRepositoryInterface
{
public function all(): Collection;
public function create(ProductDTO $dto): Product;
public function update(ProductDTO $dto, Product $product): Product;
public function delete(Product $product): bool;
public function findById(int $id): Product|null;

}