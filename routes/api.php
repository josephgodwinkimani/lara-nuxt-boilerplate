<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');
});

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {

    // Dashboard routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [DashboardController::class, 'stats']);
        Route::get('/user-growth', [DashboardController::class, 'userGrowth']);
        Route::get('/users-by-status', [DashboardController::class, 'usersByStatus']);
        Route::get('/recent-users', [DashboardController::class, 'recentUsers']);
        Route::get('/activity-metrics', [DashboardController::class, 'activityMetrics']);
    });

    // User management routes
    Route::apiResource('users', UserController::class);

    // Additional user management routes
    Route::prefix('users/{user}')->group(function () {
        Route::post('/reset-password', [UserController::class, 'resetPassword']);
        Route::post('/generate-password', [UserController::class, 'generatePassword']);
    });

});

// Health check route (public)
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'service' => 'Laravel API',
        'version' => '1.0.0',
    ]);
});
