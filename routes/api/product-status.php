<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductStatusController;

// Маршруты для проверки статуса товаров
Route::prefix('products')->group(function () {
    // Проверка статуса товара для текущего пользователя
    Route::get('/{productId}/status', [ProductStatusController::class, 'checkUserProductStatus']);
});

// Проверка статуса по уникальному коду (публичный доступ)
Route::get('/status/{uniqueCode}', [ProductStatusController::class, 'checkStatusByUniqueCode']);

// Получение всех владений пользователя
Route::get('/my-products', [ProductStatusController::class, 'getUserOwnerships']);
