<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'type',
        'description',
    ];

    public function assets(){
        return $this->hasMany(Asset::class);
    }

    public function accessories(){
        return $this->hasMany(Accessory::class);
    }

    public function components(){
        return $this->hasMany(Component::class);
    }
}
