<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetadataValue extends Model
{
    public function metadataValues()
{
    return $this->hasMany(MetadataValue::class);
}
}
