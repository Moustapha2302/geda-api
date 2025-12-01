<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Archive;

class ArchiveController extends Controller
{
    /**
     * 1. Documents arrivés J+7 (intermédiaires)
     */
    public function intermediate()
    {
        $serviceCode = request()->route('service');

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        Log::channel('audit')->info('Archive intermediate access', [
            'user_id' => $user?->id,
            'service' => $serviceCode,
        ]);

        // Le middleware own.service gère déjà l'autorisation

        $archives = Archive::where('status', 'intermediate')
            ->where('arrived_at', '<=', now()->subDays(7))
            ->get();

        return response()->json($archives);
    }

    /**
     * 2. Conservation définitive
     */
    public function final()
    {
        $serviceCode = request()->route('service');

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        Log::channel('audit')->info('Archive final access', [
            'user_id' => $user?->id,
            'service' => $serviceCode,
        ]);

        $archives = Archive::where('status', 'final')->get();

        return response()->json($archives);
    }

    /**
     * 3. Passer en conservation définitive
     */
    public function moveToFinal($serviceCode, $id)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $archive = Archive::findOrFail($id);

        if ($archive->status !== 'intermediate') {
            return response()->json([
                'error' => 'Can only move intermediate archives'
            ], 422);
        }

        $archive->update([
            'status' => 'final',
            'moved_at' => now(),
        ]);

        Log::channel('audit')->info('Archive moved to final', [
            'archive_id' => $archive->id,
            'moved_by' => $user->id,
            'service' => $serviceCode,
        ]);

        return response()->json([
            'message' => 'Moved to final',
            'archive' => $archive
        ]);
    }

    /**
     * 4. Suppression sécurisée
     */
    public function destroy($serviceCode, $id)
    {
        $archive = Archive::findOrFail($id);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Log de sécurité avant suppression
        Log::channel('audit')->info('Archive soft-deleted', [
            'archive_id' => $archive->id,
            'title' => $archive->title,
            'status' => $archive->status,
            'deleted_by' => $user->id,
            'service' => $serviceCode,
            'deleted_at' => now(),
        ]);

        $archive->delete(); // soft delete

        return response()->json([
            'message' => 'Archive securely soft-deleted'
        ]);
    }

    /**
     * 5. Échéances des 6 prochains mois
     */
    public function calendar()
    {
        $serviceCode = request()->route('service');

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        Log::channel('audit')->info('Archive calendar access', [
            'user_id' => $user?->id,
            'service' => $serviceCode,
        ]);

        $deadline = now()->addMonths(6);

        $archives = Archive::where('status', 'intermediate')
            ->where('arrived_at', '<=', $deadline)
            ->orderBy('arrived_at', 'asc')
            ->get();

        return response()->json([
            'deadline' => $deadline->format('Y-m-d'),
            'archives' => $archives,
            'count' => $archives->count(),
        ]);
    }

    /**
     * 6. Restauration d'une archive supprimée
     */
    public function restore($serviceCode, $id)
    {
        $archive = Archive::onlyTrashed()->findOrFail($id);

        $archive->restore();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        Log::channel('audit')->info('Archive restored', [
            'archive_id' => $archive->id,
            'title' => $archive->title,
            'restored_by' => $user->id,
            'service' => $serviceCode,
            'restored_at' => now(),
        ]);

        return response()->json([
            'message' => 'Archive restored',
            'archive' => $archive
        ]);
    }
}
