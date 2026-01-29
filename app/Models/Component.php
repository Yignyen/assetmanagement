<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\MorphMany;

use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    protected $fillable = [
        'name',
        'total_qty',
        'available_qty',
        'category_id',
    ];

    public function category()
{
    return $this->belongsTo(\App\Models\Category::class);
}
  
    public function assets()
    {
        return $this->belongsToMany(
            Asset::class,
            'componentS_assets'
        )->withPivot(['assigned_qty','created_by'])->withTimestamps();
    }


    public function logs()
{
    return $this->morphMany(ActionLog::class, 'item');
}

}

