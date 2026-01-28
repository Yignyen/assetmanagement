<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    protected $fillable = [
        'name',
        'total_qty',
        'available_qty',
        'category_id',
        'total_qty',

    ];
    public function category()
{
    return $this->belongsTo(\App\Models\Category::class);
}   
    public function assignments()
{
    return $this->morphMany(Assignment::class, 'item');
}


}
