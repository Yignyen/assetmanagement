<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';

    protected $fillable = [
        'name',
        'tag_color',
        'email',
        'phone',
        'fax',
        'image',
        'created_by',
        'notes',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Users belonging to this department
    public function users()
    {
        return $this->hasMany(User::class, 'department_id');
    }

    // Assets belonging to this department
    public function assets()
    {
        return $this->hasMany(Asset::class, 'department_id');
    }

    // Locations belonging to this department
    public function locations()
    {
        return $this->hasMany(Location::class, 'department_id');
    }
}
