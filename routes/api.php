<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\Api\V1\ProfileController;


Route::middleware(['api.logger', 'throttle:api'])->prefix('v1')->group(function () {
//-------------------------Auth Routes-------------------------------------------------

    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::prefix('profile')->group(function () {
            Route::put('/', [ProfileController::class, 'update']);
        });

 // |-------------------------------Comment Routes-------------------------------------------
 Route::post('articles/{article}/comments', [CommentController::class, 'store']);
 // |-------------------------------Article Routes-------------------------------------------

        Route::get('articles', [ArticleController::class, 'index']);
        Route::get('articles/{id}', [ArticleController::class, 'show'])->middleware('article.visibility');
        Route::post('articles', [ArticleController::class, 'store']);
        Route::put('articles/{article}', [ArticleController::class, 'update']);
        Route::delete('articles/{id}', [ArticleController::class, 'destroy']);
        //--------------Dashboard--------------- 
        Route::middleware(['role:admin'])->prefix('dashboard')->group(function () {
        // Get stats
        Route::get('/snapshot', [DashboardController::class, 'index']);
        
        // Trigger report generation
        Route::post('/trigger-report', [DashboardController::class, 'triggerReport']);
    });
    });
});
// |------------------- API V2-------------------------------------------------------
Route::prefix('v2')->group(function () {
    Route::get('articles', [ArticleController::class, 'index']);
});