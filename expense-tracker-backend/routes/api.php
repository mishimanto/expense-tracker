<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\AdminController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    
    // Transactions
    Route::apiResource('transactions', TransactionController::class);
    
    // Categories (read only for regular users)
    Route::get('/categories', [CategoryController::class, 'index']);
    
    // Reports
    Route::get('/reports/dashboard', [ReportController::class, 'dashboard']);
    Route::get('/reports/monthly', [ReportController::class, 'monthlyReport']);
    
    // Admin routes
    Route::middleware('admin')->group(function () {
        Route::apiResource('categories', CategoryController::class)->except(['index']);
        Route::get('/admin/users', [AdminController::class, 'users']);
        Route::put('/admin/users/{id}', [AdminController::class, 'updateUser']);
        Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);
        Route::get('/admin/dashboard', [AdminController::class, 'adminDashboard']);
    });
});