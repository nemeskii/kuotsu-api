<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DonorController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [DonorController::class, 'store']);
Route::post('/admin/login', [AuthController::class, 'login']);
Route::get('/blood-inventory', [DonorController::class, 'inventory']);

// Protected admin routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/logout', [AuthController::class, 'logout']);
    Route::get('/admin/me', [AuthController::class, 'me']);

    Route::get('/admin/donors', [DonorController::class, 'index']);
    Route::get('/admin/donors/{donor}', [DonorController::class, 'show']);
    Route::put('/admin/donors/{donor}', [DonorController::class, 'update']);
    Route::delete('/admin/donors/{donor}', [DonorController::class, 'destroy']);
});