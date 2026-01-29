<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Auth;
use App\Models\ActionLog;




class ActivityLogger
{
    public static function log($action, $item, $target = null, $note = null, $qty = 1)
    {
        return ActionLog::create([
            'created_by'  => Auth::id() ?? 1, // clean + IDE safe
            'action_type' => $action,

            'item_type'   => get_class($item),
            'item_id'     => $item->id,

            'target_type' => $target ? get_class($target) : null,
            'target_id'   => $target?->id,

            'note'        => $note,
            'quantity'    => $qty,
            'action_date' => now(),
        ]);
    }
}
