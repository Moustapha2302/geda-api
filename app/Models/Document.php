<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'service_id',
        'user_id',
        'title',
        'description',
        'file_path',
        'md5',
        'status',
        'arrived_at',
        'moved_at',
        'ocr_text',
        'ocr_status',
        'ocr_processed_at',
        'ocr_error',
        'ocr_confidence',
    ];

    protected $casts = [
        'ocr_processed_at' => 'datetime',
         'arrived_at' => 'datetime',
         'moved_at' => 'datetime',
        'ocr_confidence' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = [
        'has_ocr',
        'ocr_preview'
    ];

    /**
     * Relations
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accesseurs
     */
    public function getHasOcrAttribute(): bool
    {
        return !empty($this->ocr_text) && $this->ocr_status === 'ocr_done';
    }

    public function getOcrPreviewAttribute(): ?string
    {
        if (!$this->ocr_text) {
            return null;
        }

        return mb_substr($this->ocr_text, 0, 200) .
               (mb_strlen($this->ocr_text) > 200 ? '...' : '');
    }

    /**
     * Scopes
     */
    public function scopeWithOcr($query)
    {
        return $query->whereNotNull('ocr_text')
                     ->where('ocr_status', 'ocr_done');
    }

    public function scopePendingOcr($query)
    {
        return $query->where(function($q) {
            $q->whereNull('ocr_text')
              ->orWhere('ocr_status', 'pending');
        });
    }

    public function scopeFailedOcr($query)
    {
        return $query->where('ocr_status', 'ocr_failed');
    }

    /**
     * Méthodes utilitaires
     */
    public function needsOcr(): bool
    {
        $ocrExtensions = ['png', 'jpg', 'jpeg', 'pdf'];
        $extension = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));

        return in_array($extension, $ocrExtensions) && empty($this->ocr_text);
    }

    public function canRetryOcr(): bool
    {
        return $this->ocr_status === 'ocr_failed';
    }

    public function isOcrProcessing(): bool
    {
        return $this->ocr_status === 'processing';
    }

    public function metadataValues()
{
    return $this->hasMany(MetadataValue::class);
}
}
