<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'type',
        'description',
    ];

    public function models()
{
    return $this->hasMany(AssetModel::class, 'category_id');
}


    

}
