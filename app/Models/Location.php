<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'notes',
        'department_id', // 🔑 REQUIRED
    ];

    /* =======================
     * RELATIONSHIPS
     * ======================= */

    // Assets physically here (optional)
    public function assets()
    {
        return $this->hasMany(Asset::class, 'location_id');
    }

    // Users optionally assigned to this location
    public function users()
    {
        return $this->hasMany(User::class, 'location_id');
    }

    // Tenant ownership
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
