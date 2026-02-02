<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActionLog extends Model
{
     protected $fillable = [
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

    // Who performed the action
    public function actor()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    // Polymorphic: the thing being acted on (Asset, Accessory, Component)
    public function item()
    {
        return $this->morphTo();
    }

    // Polymorphic: target (User, Asset, etc.)
    public function target()
    {
        return $this->morphTo()->withTrashed();
    }
}
