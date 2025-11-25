<?php

namespace App\Http\Controllers\Api;

use App\Models\Document;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class SearchController extends Controller
{
    // Multi-critères
    public function index(Request $request)
    {
        $q = Document::query()
            ->when($request->service, fn($b, $v) => $b->where('service_id', $v))
            ->when($request->type, fn($b, $v) => $b->where('type_id', $v))
            ->when($request->year, fn($b, $v) => $b->whereYear('created_at', $v))
            ->when($request->q, fn($b, $v) => $b->where('title', 'like', "%$v%"));

        return response()->json([
            'success' => true,
            'data' => $q->paginate(20),
            'message' => 'Résultats de recherche'
        ]);
    }

    // Plein-texte OCR (placeholder)
    public function ocr(Request $request)
    {
        // TODO : Elasticsearch ou PostgreSQL tsvector
        return response()->json([
            'success' => true,
            'data' => [],
            'message' => 'Recherche plein-texte (non implémentée)'
        ]);
    }

    // Autocomplete
    public function suggest(Request $request)
    {
        $q = $request->input('q', '');
        $titles = Document::where('title', 'like', "$q%")
            ->limit(5)
            ->pluck('title');
        return response()->json([
            'success' => true,
            'data' => $titles,
            'message' => 'Suggestions'
        ]);
    }
}
