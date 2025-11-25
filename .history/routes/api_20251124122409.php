<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\TwoFAController;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'update']);

    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{id}', [ServiceController::class, 'show']);

    Route::get('/search', [SearchController::class, 'index']);
    Route::get('/search/ocr', [SearchController::class, 'ocr']);
    Route::get('/suggestions', [SearchController::class, 'suggest']);

    Route::post('/2fa/enable', [TwoFAController::class, 'enable']);
    Route::post('/2fa/verify', [TwoFAController::class, 'verify']);
});
