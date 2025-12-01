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
use App\Http\Controllers\Api\MetadataTypeController;
use App\Http\Controllers\Api\MetadataController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\ArchiveController;
use App\Http\Controllers\Api\AdminController;   // <-- ajout

use Illuminate\Support\Facades\Gate;

Gate::define('admin', function ($user) {
    \Log::channel('audit')->info('=== GATE ADMIN CHECK (from routes) ===', [
        'user_id' => $user->id,
        'has_admin_role' => $user->roles()->where('name', 'admin')->exists(),
    ]);

    return $user->roles()->where('name', 'admin')->exists();
});

// --------------------------------------------------
// 1. ROUTES PUBLIQUES
// --------------------------------------------------
Route::post('/login', [AuthController::class, 'login']);

// --------------------------------------------------
// 2. ROUTES CRON (sans authentification)
// --------------------------------------------------
Route::prefix('{service}/cron')->group(function () {
    Route::post('ocr-pending', [CronController::class, 'ocrPending']);
    Route::get('ocr-stats', [CronController::class, 'ocrStats']);
    Route::post('ocr-retry-failed', [CronController::class, 'ocrRetryFailed']);
    Route::post('ocr-force/{documentId}', [CronController::class, 'ocrForce']);
    Route::post('auto-transfer-j7', [CronController::class, 'autoTransferJ7']);
Route::post('alert-before-end', [CronController::class, 'alertBeforeEnd']);
});

// --------------------------------------------------
// 3. ROUTES PROTÉGÉES (auth:sanctum)
// --------------------------------------------------
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

    // --------------------------------------------------
    // 4. ROUTES ADMIN (hors préfixe service)
    // --------------------------------------------------
    Route::prefix('admin')->middleware('can:admin')->group(function () {
        Route::get('/stats',   [AdminController::class, 'stats']);
        Route::get('/logs',    [AdminController::class, 'logs']);
        Route::post('/backup', [AdminController::class, 'backup']);
        Route::get('/exports', [AdminController::class, 'exports']);
    });

    // --------------------------------------------------
    // 5. ROUTES PAR SERVICE (own.service)
    // --------------------------------------------------
    Route::middleware('own.service')->group(function () {

        // Documents
        Route::get('{service}/documents', [DocumentController::class, 'index']);
        Route::post('{service}/documents', [DocumentController::class, 'store']);
        Route::post('{service}/documents/batch', [DocumentController::class, 'batchStore']);
        Route::get('{service}/documents/{id}/preview', [DocumentController::class, 'preview']);
        Route::get('{service}/documents/{id}/download', [DocumentController::class, 'download'])->name('documents.download')->middleware('signed');
        Route::get('{service}/documents/{id}', [DocumentController::class, 'show']);
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

        // Transfers
        Route::prefix('{service}/transfers')->group(function () {
            Route::post('/', [TransferController::class, 'store']);
            Route::get('/', [TransferController::class, 'index']);
            Route::get('{id}', [TransferController::class, 'show']);
            Route::post('{id}/accept', [TransferController::class, 'accept']);
            Route::post('{id}/reject', [TransferController::class, 'reject']);
            Route::post('{id}/share', [TransferController::class, 'shareExternal']);
        });

        // Metadata Types
        Route::apiResource('{service}/metadata-types', MetadataTypeController::class);

        // Metadata
        Route::get('{service}/metadata/{document}', [MetadataController::class, 'show']);
        Route::put('{service}/metadata/{document}', [MetadataController::class, 'update']);

        // Files
        Route::post('{service}/files', [FileController::class, 'upload']);
        Route::delete('{service}/files/{uuid}', [FileController::class, 'destroy']);
        Route::get('{service}/files/{uuid}/download', [FileController::class, 'download'])->name('files.download');

        // Archives
        Route::prefix('{service}/archives')->group(function () {
            Route::get('intermediate', [ArchiveController::class, 'intermediate']);
            Route::get('final', [ArchiveController::class, 'final']);
            Route::post('{id}/move', [ArchiveController::class, 'moveToFinal']);
            Route::delete('{id}/destroy', [ArchiveController::class, 'destroy']);
            Route::get('calendar', [ArchiveController::class, 'calendar']);
            Route::post('{id}/restore', [ArchiveController::class, 'restore']);
        });
    });
});
