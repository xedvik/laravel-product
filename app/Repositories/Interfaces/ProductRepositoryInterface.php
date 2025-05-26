<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use App\DTO\Products\ProductDTO;
use App\Models\Product;
interface ProductRepositoryInterface
{
    // Метод для получения всех товаров
public function all(): Collection;
// Метод для создания товара
public function create(ProductDTO $dto): Product;
// Метод для обновления товара
public function update(ProductDTO $dto, Product $product): Product;
// Метод для удаления товара
public function delete(Product $product): bool;
// Метод для поиска товара по ID
public function findById(int $id): Product|null;

}