<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetadataType extends Model
{
    use HasFactory;

    /**
     * Attributs mass-assignables.
     */
    protected $fillable = [
        'service_id',
        'name',
        'fields',
    ];

    /**
     * Cast des colonnes JSON.
     */
    protected $casts = [
        'fields' => 'array',
    ];

    /*--------------------------------------------------------------------------*/
    /* Relations                                                                */
    /*--------------------------------------------------------------------------*/

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
