<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\MorphMany;

class Accessory extends Model
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

// Accessory checkout history

public function checkouts()
{
    return $this->hasMany(AccessoryCheckout::class);
}

public function logs()
{
    return $this->morphMany(ActionLog::class, 'item');
}


}
