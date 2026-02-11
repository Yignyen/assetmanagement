<?php

namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\StatusLabel;
use App\Support\DepartmentContext;
use Illuminate\Http\Request;

class AssetCheckinController extends Controller
{
    /**
     * Handle Asset Check-In
     */
    public function store(Request $request, Asset $asset)
    {
        // 🔐 Department Security
        if ($asset->department_id !== DepartmentContext::id()) {
            abort(403);
        }

        // 🛑 Must be assigned
        if ($asset->assigned_to === null) {
            return back()->with('error', 'Asset is already unassigned.');
        }

        // ✅ Validate
        $request->validate([
            'status_id' => 'required|exists:status_labels,id',
            'note'      => 'nullable|string',
        ]);

        // 🚀 Perform Check-In
        $asset->checkIn(
            note: $request->note,
            statusId: $request->status_id
        );

        return redirect()
            ->route('assets.show', $asset)
            ->with('success', 'Asset checked in successfully.');
    }
}
