<?php

namespace App\Helpers;

use App\Models\ActionLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;

class ActivityLogger
{
    public static function log(
        string $action,
        Model $item,
        ?Model $target = null,
        ?string $note = null,
        int $qty = 1
    ): ActionLog {

        // ✅ Correct Eloquent-safe check
        if (!isset($item->department_id)) {
            throw new Exception('Cannot log activity: item has no department_id');
        }

        return ActionLog::create([
            // 🔑 ALWAYS derive from the item
            'department_id' => $item->department_id,

            // Actor (system-safe)
            'created_by' => Auth::id() ?? 1,

            'action_type' => $action,

            'item_type' => get_class($item),
            'item_id'   => $item->id,

            'target_type' => $target ? get_class($target) : null,
            'target_id'   => $target?->id,

            'note'        => $note,
            'quantity'    => $qty,
            'action_date' => now(),
        ]);
    }
}
