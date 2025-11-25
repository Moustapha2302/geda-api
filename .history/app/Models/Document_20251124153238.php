<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
    'uuid', 'title', 'file_path', 'service_id', 'user_id', 'md5', 'status'
];
}
