<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'name',
        'serial_no',
        'asset_tag',
        'status',
        'category_id',
        'purchase_date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Asset can be assigned many times (polymorphic)
    public function assignments()
    {
        return $this->morphMany(Assignment::class, 'item');
    }
    
}
