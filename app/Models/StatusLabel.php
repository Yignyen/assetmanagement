<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusLabel extends Model
{
    use SoftDeletes;

    protected $table = 'status_labels';

    protected $fillable = [
        'name',
        'notes',
        'deployable',
        'pending',
        'archived',
        'color',
        'default_label',
    ];

    protected $casts = [               /*    type casting , instead of getting 1 we get true */
        'deployable' => 'boolean',
        'pending' => 'boolean',
        'archived' => 'boolean',
        'default_label' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function assets()
    {
        return $this->hasMany(Asset::class, 'status_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods,  loguc helper
    |--------------------------------------------------------------------------
    */

    public function isDeployable(): bool
    {
        return $this->deployable && !$this->archived && !$this->pending;    //is true onlt, deployable-true,archieved-false, pending-flase
    }

    public function isArchived(): bool    //Simple boolean check.
    {
        return $this->archived;
    }

    public function isPending(): bool   //For approval workflows later.
    {
        return $this->pending;
    }
//So instead of checking 3 columns everywhere, you just call:
    public function getType(): string   //This converts flags into readable type.
    {
        if ($this->pending) return 'pending';
        if ($this->archived) return 'archived';
        if (!$this->deployable) return 'undeployable';

        return 'deployable';
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    *///Scopes make queries readable. query scope is reusable query filter inside model , stead of where() confotion everywehre, you define them once and reuse them.

    public function scopeDeployable($query)
    {
        return $query->where('deployable', true)
                     ->where('archived', false)
                     ->where('pending', false);
    }

    public function scopeArchived($query)
    {
        return $query->where('archived', true);
    }

    public function scopePending($query)
    {
        return $query->where('pending', true);
    }

    public function scopeUndeployable($query)
    {
        return $query->where('deployable', false)
                     ->where('archived', false)
                     ->where('pending', false);
    }
}
