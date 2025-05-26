<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RentController;

Route::post('/rent', [RentController::class, 'rent']);
Route::post('/extend-rent', [RentController::class, 'extendRent']);