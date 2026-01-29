<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
        'assigned_type',
        'assigned_to',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

  
   

//polymorphic owner, directly assigned these two on assets .
    public function assigned()
{
    return $this->morphTo(null, 'assigned_type', 'assigned_to');
}

    /**
     * An asset can have many components attached to it.
     * Example:
     *  - Laptop asset has RAM, SSD, Battery as components.
     *  - One asset can use multiple quantities of the same component.
     *
     * This is a Many-to-Many relationship using the pivot table: components_assets
     *
     * Pivot table columns:
     *  - asset_id        → which asset
     *  - component_id    → which component
     *  - assigned_qty    → how many units are used in this asset
     *  - note            → optional comment about assignment
     *  - created_by      → admin/user who made the assignment
     *  - created_at / updated_at → timestamps of assignment
     */
    public function components()
    {
        return $this->belongsToMany(
            Component::class,
            'components_assets'
        )->withPivot(['assigned_qty', 'note', 'created_by'])
         ->withTimestamps();
    }

    public function logs()
{
    return $this->morphMany(ActionLog::class, 'item');
}

    
}
