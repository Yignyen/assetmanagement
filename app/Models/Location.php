<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    // app/Models/Location.php

    use SoftDeletes;

    protected $fillable = ['name', 'parent_id', 'notes'];

    protected $casts = [
        'parent_id' => 'integer',
    ];

    // Parent department
    public function parent()
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    // Child rooms
    public function children()
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    // Assets physically here
    public function assets()
    {
        return $this->hasMany(Asset::class, 'location_id');
    }

    // Users (department only)
    public function users()
    {
        return $this->hasMany(User::class, 'location_id');
    }

    // Helpers
    public function isDepartment(): bool
    {
        return $this->parent_id === null;
    }

    public function isPlace(): bool
    {
        return $this->parent_id !== null;
    }

    // Scopes
    public function scopeDepartments($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopePlaces($query)
    {
        return $query->whereNotNull('parent_id');
    }
}


