<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\TwoFAController;
use App\Http\Controllers\Api\DocumentController;

use App\Http\Controllers\Api\SignatureController;

// ---------- 1. ROUTES PUBLIQUES ----------
Route::post('/login', [AuthController::class, 'login']);

// ---------- 2. ROUTES PROTÉGÉES ----------
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

    // ---------- 3. ROUTES PAR SERVICE ----------
    Route::middleware('own.service')->group(function () {
        // Liste et création
        Route::get('{service}/documents', [DocumentController::class, 'index']);
        Route::post('{service}/documents', [DocumentController::class, 'store']); 
        Route::post('{service}/documents/batch', [DocumentController::class, 'batchStore']);
        
        // ⚠️ IMPORTANT : Routes spécifiques AVANT les génériques
        Route::get('{service}/documents/{id}/preview', [DocumentController::class, 'preview']);
        Route::get('{service}/documents/{id}/download', [DocumentController::class, 'download'])
             ->name('documents.download')
             ->middleware('signed');
        
        // Route générique (doit être APRÈS preview/download)
        Route::get('{service}/documents/{id}', [DocumentController::class, 'show']);
        
        // Modification et suppression
        Route::put('{service}/documents/{id}', [DocumentController::class, 'update']);
        Route::delete('{service}/documents/{id}', [DocumentController::class, 'destroy']);

        Route::post('{service}/documents/{id}/sign', [SignatureController::class, 'sign']);
    });
});