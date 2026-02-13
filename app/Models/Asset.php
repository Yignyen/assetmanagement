<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Helpers\ActivityLogger;
use Exception;
use App\Models\StatusLabel;
/**
 * @property \App\Models\StatusLabel|null $status
 */

class Asset extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'serial_no',
        'asset_tag',
        'status_id',
        'model_id',
        'category_id',
        'purchase_date',
        'location_id',
        'assigned_type',
        'assigned_to',
        'department_id',
        'label'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Boot Method (Auto-generate name)
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::saving(function (Asset $asset) {

            $modelName = $asset->model?->name;

            if (!$modelName && $asset->model_id) {
                $modelName = AssetModel::withTrashed()
                    ->find($asset->model_id)?->name;
            }

            $parts = [
                $modelName,
                $asset->asset_tag,
                $asset->label,
            ];

            $asset->name = implode(' - ', array_filter($parts));
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function status()
    {
        return $this->belongsTo(StatusLabel::class, 'status_id');
    }

    public function model()
    {
        return $this->belongsTo(AssetModel::class, 'model_id')->withTrashed();
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

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
        return $this->morphMany(ActionLog::class, 'item')->withTrashed();
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    
    
    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */

    /**
     * CHECK IN
     */
    public function checkIn(?string $note = null, ?int $statusId = null): void
{
    if ($this->assigned_to === null) {
        throw new Exception('Asset is not currently assigned.');
    }

    $previousTarget = $this->assigned;

    // If status provided, use it
    if ($statusId) {
        $this->status_id = $statusId;
    } else {
        // fallback to default status
        $defaultStatus = StatusLabel::where('default_label', true)->first();
        $this->status_id = $defaultStatus?->id;
    }

    $this->assigned_to   = null;
    $this->assigned_type = null;
    $this->location_id   = null;
    $this->assigned_at   = null;

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
     * CHECK OUT TO USER
     */
    public function checkOutToUser(User $user, ?string $note = null): void
    {
    
        if ($this->assigned_to !== null) {
        $this->checkIn();
    }

        $this->assigned_to   = $user->id;
        $this->assigned_type = User::class;
        $this->location_id   = $user->location_id;
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
     * CHECK OUT TO LOCATION
     */
    public function checkOutToLocation(Location $location, ?string $note = null): void
    {
        if ($this->assigned_to !== null) {
        $this->checkIn();
    }
        
        $this->assigned_to   = $location->id;
        $this->assigned_type = Location::class;
        $this->location_id   = $location->id;
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
     * CHECK OUT TO ANOTHER ASSET
     */
    public function checkOutToAsset(Asset $parentAsset, ?string $note = null): void
    {
       if ($this->assigned_to !== null) {
        $this->checkIn();
    }

        $this->assigned_to   = $parentAsset->id;
        $this->assigned_type = Asset::class;
        $this->location_id   = $parentAsset->location_id;
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

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

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
