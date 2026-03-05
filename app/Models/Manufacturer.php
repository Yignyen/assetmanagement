<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Manufacturer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'support_email',
        'support_phone',
        'support_url',
        'warranty_lookup_url',
        'notes'
    ];

    public function models()
    {
        return $this->hasMany(\App\Models\AssetModel::class);
    }
}