<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'user_id',
        'item_type',
        'item_id',
        'assigned_at',
        'returned_at',
        'status',
        'assigned_by',
        'notes',
    ];

    /**
     * Relationships
     */

    // The assigned item (Asset / Accessory / Component)
    public function item()
    {
        return $this->morphTo();
    }

    // User who received the item
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Admin who assigned the item
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
