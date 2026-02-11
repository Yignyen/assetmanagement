<?php

namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\User;
use App\Models\Location;
use App\Support\DepartmentContext;
use Illuminate\Http\Request;

class AssetCheckoutController extends Controller
{
    /**
     * Checkout asset
     */
    public function store(Request $request, Asset $asset)
    {
        $departmentId = DepartmentContext::id();

        if ($asset->department_id !== $departmentId) {
            abort(403);
        }

        $request->validate([
            'checkout_to_type' => 'required|in:user,location,asset',
            'checkout_to_id'   => 'required|integer',
            'note'             => 'nullable|string',
        ]);

        match ($request->checkout_to_type) {
            'user' => $asset->checkOutToUser(
                User::where('department_id', $departmentId)
                    ->findOrFail($request->checkout_to_id),
                $request->note
            ),

            'location' => $asset->checkOutToLocation(
                Location::where('department_id', $departmentId)
                    ->findOrFail($request->checkout_to_id),
                $request->note
            ),

            'asset' => $asset->checkOutToAsset(
                Asset::where('department_id', $departmentId)
                    ->findOrFail($request->checkout_to_id),
                $request->note
            ),
        };

        return back()->with('success', 'Asset checked out successfully');
    }
}
