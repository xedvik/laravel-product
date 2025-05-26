<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::get('/user', [UserController::class, 'getUserInfo']);
Route::get('/user/balance', [UserController::class, 'getUserBalance']);
Route::post('/user/balance', [UserController::class, 'updateUserBalance']);