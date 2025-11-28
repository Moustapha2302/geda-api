<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\MetadataValue;
use Illuminate\Http\Request;

class MetadataController extends Controller
{
    public function show(Request $request, int $service, Document $document)
    {
        abort_if($document->service_id !== $service, 403);
        return $document->metadataValues()->with('metadataType:id,name')->get();
    }

    public function update(Request $request, int $service, Document $document)
    {
        abort_if($document->service_id !== $service, 403);

        $validated = $request->validate([
            'values' => 'required|array',
            'values.*.metadata_type_id' => 'required|exists:metadata_types,id,service_id,'.$service,
            'values.*.value' => 'required',
        ]);

        foreach ($validated['values'] as $v) {
            MetadataValue::updateOrCreate(
                [
                    'document_id' => $document->id,
                    'metadata_type_id' => $v['metadata_type_id'],
                ],
                ['value' => $v['value']]
            );
        }

        return response()->noContent();
    }
}
