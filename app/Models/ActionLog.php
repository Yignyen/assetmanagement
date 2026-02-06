<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Support\DepartmentContext;

class ActionLog extends Model
{
    protected $fillable = [
        'department_id',   // 🔑 REQUIRED
        'created_by',
        'action_type',
        'item_type',
        'item_id',
        'target_type',
        'target_id',
        'note',
        'quantity',
        'action_date',
    ];

    protected $casts = [
        'action_date' => 'datetime',
    ];

    /* =======================
     * GLOBAL SCOPE (TENANT SAFETY)
     * ======================= */
    protected static function booted()
    {
        static::addGlobalScope('department', function ($query) {
            $query->where(
                'department_id',
                DepartmentContext::id()
            );
        });
    }

    /* =======================
     * RELATIONSHIPS
     * ======================= */

    // Who performed the action
    public function actor()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    // Polymorphic: the thing being acted on (Asset, User, Location, etc.)
    public function item()
    {
        return $this->morphTo()->withTrashed(); // aslo soft deletes.
    }

    // Polymorphic: target (User, Asset, etc.)
    public function target()
    {
        return $this->morphTo()->withTrashed();
    }

    // Tenant ownership
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
