<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessoryCheckout extends Model
{
    protected $table = 'accessories_checkout';

    protected $fillable = [
        'accessory_id',
        'assigned_to',
        'assigned_type',
        'created_by',
        'note',
        'checked_out_at',
        'returned_at',
    ];

    // The accessory itself
    public function accessory()
    {
        return $this->belongsTo(Accessory::class);
    }

    // Polymorphic target (User, Asset, later Location)
    public function assigned()
    {
        return $this->morphTo(null, 'assigned_type', 'assigned_to');
    }

    // Admin who issued
    public function admin()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
