<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\TwoFAController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\CronController;
use App\Http\Controllers\Api\WorkflowController;
use App\Http\Controllers\Api\SignatureController;
use App\Http\Controllers\Api\TransferController;

// ---------- 1. ROUTES PUBLIQUES ----------
Route::post('/login', [AuthController::class, 'login']);

// ---------- 2. ROUTES CRON (sans authentification) ----------
Route::prefix('{service}/cron')->group(function () {
    // Traiter les documents en attente d'OCR
    Route::post('ocr-pending', [CronController::class, 'ocrPending']);

    // Statistiques OCR
    Route::get('ocr-stats', [CronController::class, 'ocrStats']);

    // Réessayer les documents échoués
    Route::post('ocr-retry-failed', [CronController::class, 'ocrRetryFailed']);

    // Forcer le retraitement d'un document
    Route::post('ocr-force/{documentId}', [CronController::class, 'ocrForce']);
});

// ---------- 3. ROUTES PROTÉGÉES ----------
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

    // ---------- 4. ROUTES PAR SERVICE ----------
    Route::middleware('own.service')->group(function () {
        // Liste et création
        Route::get('{service}/documents', [DocumentController::class, 'index']);
        Route::post('{service}/documents', [DocumentController::class, 'store']);
        Route::post('{service}/documents/batch', [DocumentController::class, 'batchStore']);

        // Routes spécifiques AVANT les génériques
        Route::get('{service}/documents/{id}/preview', [DocumentController::class, 'preview']);
        Route::get('{service}/documents/{id}/download', [DocumentController::class, 'download'])
             ->name('documents.download')
             ->middleware('signed');

        // Route générique (doit être APRÈS preview/download)
        Route::get('{service}/documents/{id}', [DocumentController::class, 'show']);

        // Modification et suppression
        Route::put('{service}/documents/{id}', [DocumentController::class, 'update']);
        Route::delete('{service}/documents/{id}', [DocumentController::class, 'destroy']);

        // Signature
        Route::post('{service}/documents/{id}/sign', [SignatureController::class, 'sign']);

        // Workflow
        Route::get('{service}/workflows/pending', [WorkflowController::class, 'pending']);

        Route::get('{service}/workflows/{document}', [WorkflowController::class, 'show']);
        Route::post('{service}/workflows/{document}/start', [WorkflowController::class, 'start']);
        Route::post('{service}/workflows/{document}/validate', [WorkflowController::class, 'validate']);
        Route::post('{service}/workflows/{document}/reject', [WorkflowController::class, 'reject']);

        // routes/api.php (dans le groupe own.service)
// Route::prefix('{service}/transfers')->group(function () {

   Route::post('/', [TransferController::class, 'store']);       // initier transfert
    Route::get('/', [TransferController::class, 'index']);        // liste des transferts
    Route::get('{id}', [TransferController::class, 'show']);      // détail d’un transfert
    Route::post('{id}/accept', [TransferController::class, 'accept']); // accepter
    Route::post('{id}/reject', [TransferController::class, 'reject']); // rejeter + motif
    Route::post('{id}/share', [TransferController::class, 'shareExternal']); // partage externe
});
       });

