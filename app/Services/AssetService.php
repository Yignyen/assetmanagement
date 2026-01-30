<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\User;
use App\Helpers\ActivityLogger;
use Exception;

class AssetService
{
    public static function checkout(Asset $asset, User $user, ?string $note = null): void
    {

    // NOTE: Status-based guard for now.
// When assignment history / check-in is added,
// this should validate active assignment instead.

        // 1. Guard: asset already assigned
        if ($asset->status !== 'available') {
            throw new Exception('Asset is not available for assignment');
        }

        // 2. Assign asset
        $asset->update([
            'assigned_to'   => $user->id,
            'assigned_type' => User::class,
            'status'        => 'assigned',
        ]);

        // 3. Log activity
        ActivityLogger::log(
            action: 'checkout',
            item: $asset,
            target: $user,
            note: $note,
            qty: 1
        );
    }

    public static function checkin(Asset $asset, ?string $note = null): void
{
    // Guard: asset must be assigned
    if ($asset->status !== 'assigned') {
        throw new Exception('Asset is not currently assigned');
    }

    // Unassign asset
    $asset->update([
        'assigned_to'   => null,
        'assigned_type' => null,
        'status'        => 'available',
    ]);

    // Log activity
    ActivityLogger::log(
        action: 'checkin',
        item: $asset,
        target: null,
        note: $note,
        qty: 1
    );
}
    
}
