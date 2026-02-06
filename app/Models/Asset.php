<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\ActivityLogger;
use Exception;

class Asset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'serial_no',
        'asset_tag',
        'status',
        'model_id',
        'category_id',
        'purchase_date',
        'location_id',
        'assigned_type',
        'assigned_to',
        'department_id'

    ];


    protected $casts = [
    'assigned_at' => 'datetime',
];

    /* =======================
     * RELATIONSHIPS
     * ======================= */

 public function model()
{
    return $this->belongsTo(AssetModel::class, 'model_id')
        ->withTrashed();
}


    // Physical location (department OR room)
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    // Polymorphic assignment target (history-safe)
    public function assigned()
    {
        return $this->morphTo('assigned', 'assigned_type', 'assigned_to')
                    ->withTrashed();
    }

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
        return $this->morphMany(ActionLog::class, 'item')->withTrashed();;
    }

    /* =======================
     * HELPERS
     * ======================= */

    protected function guardAvailable(): void
    {
        if ($this->assigned_to !== null) {
            throw new Exception('Asset is already assigned');
        }
    }

    /* =======================
     * BUSINESS LOGIC
     * ======================= */

    /**
     * CHECK IN (return to department pool)
     */
    public function checkIn(?string $note = null): void
    {
        if ($this->assigned_to === null) {
            throw new Exception('Asset is not currently assigned');
        }

        // capture previous target for log
        $previousTarget = $this->assigned;

        $this->assigned_to   = null;
        $this->assigned_type = null;
        $this->status        = 'available';
        $this->location_id   = null;
        $this->assigned_at   = null; // ✅ clear

        $this->save();

        ActivityLogger::log(
            action: 'checkin',
            item: $this,
            target: $previousTarget,
            note: $note,
            qty: 1
        );
    }

    /**
     * CHECK OUT to USER
     */
    public function checkOutToUser(User $user, ?string $note = null): void
    {
        $this->guardAvailable();

        $this->assigned_to   = $user->id;
        $this->assigned_type = User::class;
        $this->location_id   = $user->location_id;
        $this->status        = 'assigned';
        $this->assigned_at   = now();

        $this->save();

        ActivityLogger::log(
            action: 'checkout',
            item: $this,
            target: $user,
            note: $note,
            qty: 1
        );
    }

    /**
     * CHECK OUT to LOCATION (room)
     */
    public function checkOutToLocation(Location $location, ?string $note = null): void
    {
        $this->guardAvailable();

        
        $this->assigned_to   = $location->id;
        $this->assigned_type = Location::class;
        $this->location_id   = $location->id;
        $this->status        = 'assigned';
        $this->assigned_at   = now();

        $this->save();

        ActivityLogger::log(
            action: 'checkout',
            item: $this,
            target: $location,
            note: $note,
            qty: 1
        );
    }

    /**
     * CHECK OUT to ANOTHER ASSET
     */
    public function checkOutToAsset(Asset $parentAsset, ?string $note = null): void
    {
        $this->guardAvailable();

        $this->assigned_to   = $parentAsset->id;
        $this->assigned_type = Asset::class;
        $this->location_id   = $parentAsset->location_id;
        $this->status        = 'assigned';
        $this->assigned_at   = now();

        $this->save();

        ActivityLogger::log(
            action: 'checkout',
            item: $this,
            target: $parentAsset,
            note: $note,
            qty: 1
        );
    }



    public function department()
{
    return $this->belongsTo(Department::class, 'department_id');
}


public function getDisplayNameAttribute(): string
{
    $model = $this->model?->name ?? 'Unknown Model';
    $serial = $this->serial_no ?? '—';

    return "{$model} – {$serial}";
}

    
public function getCategoryNameAttribute(): string
{
    return $this->model?->category?->name ?? '—';
}



}
