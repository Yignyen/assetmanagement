<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetModel extends Model
{
    use SoftDeletes;

    protected $table = 'models';

    protected $fillable = [
        'name',
        'category_id',
        'require_serial',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'model_id');
    }
}
