<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['code', 'name', 'logo'];

    public function types()
    {
        return $this->hasMany(Type::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
