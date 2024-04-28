<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

#Product
Route::get('/product', [ProductController::class, 'getList'])->middleware('auth:sanctum');
Route::get('/product/{id}', [ProductController::class, 'getOne'])->middleware('auth:sanctum');
Route::post('/product', [ProductController::class, 'create'])->middleware('auth:sanctum');
Route::put('/product/{id}', [ProductController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/product/{id}', [ProductController::class, 'delete'])->middleware('auth:sanctum');
