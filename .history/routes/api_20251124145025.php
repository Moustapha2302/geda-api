<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\TwoFAController;
use App\Http\Controllers\Api\DocumentController;

// ---------- 1. ROUTES PUBLIQUES ----------
Route::post('/login', [AuthController::class, 'login']);

// ---------- 2. ROUTES PROTÉGÉES (auth + own.service pour les routes par service) ----------
Route::middleware('auth:sanctum')->group(function () {

    // Auth & profil
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'update']);

    // Services
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{id}', [ServiceController::class, 'show']);

    // Recherche globale
    Route::get('/search', [SearchController::class, 'index']);
    Route::get('/search/ocr', [SearchController::class, 'ocr']);
    Route::get('/suggestions', [SearchController::class, 'suggest']);

    // 2FA
    Route::post('/2fa/enable', [TwoFAController::class, 'enable']);
    Route::post('/2fa/verify', [TwoFAController::class, 'verify']);

    // ---------- 3. ROUTES PAR SERVICE (sécurisées par own.service) ----------
     Route::middleware(['auth:sanctum', 'own.service'])->group(function () {
    Route::get('{service}/documents', [DocumentController::class, 'index']);
    Route::post('{service}/documents', [DocumentController::class, 'store']); // ← nouvelle ligne
});
});
