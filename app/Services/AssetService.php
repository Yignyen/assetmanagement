<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\User;
use App\Helpers\ActivityLogger;
use Exception;

class AssetService
{
    public static function checkout(Asset $asset, User $user, ?string $note = null): void    //static is easy to call anyhweere,  void  no need to return data,it jujst act
    {

    // NOTE: Status-based guard for now.
// When assignment history / check-in is added,
// this should validate active assignment instead.

        // 1. Guard: based on real state
        if ($asset->assigned_to !== null ) {
            throw new Exception('Asset is not available for assignment');
        }

        //2. let the model handle assignment
        $asset->checkOutTo($user);

        // 2. Assign asset
      /*   $asset->update([
            'assigned_to'   => $user->id,
            'assigned_type' => User::class,
            'status'        => 'assigned',
        ]); */

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
    if ($asset->assigned_to === null) {
        throw new Exception('Asset is not currently assigned');
    }

       // ✅ 1. Capture who it was assigned to BEFORE check-in
    $previousTarget = $asset->assigned; // User / Location / Asset (withTrashed-safe)
//then next method will unassigned.
    //let the mmodel handle unassignment
    $asset->checkIn();

   /*  
    // Unassign asset
    $asset->update([
        'assigned_to'   => null,
        'assigned_type' => null,
        'status'        => 'available',
    ]);
 */
    // Log activity
    ActivityLogger::log(
        action: 'checkin',
        item: $asset,
        target: $previousTarget,
        note: $note,
        qty: 1
    );
}
    
}
