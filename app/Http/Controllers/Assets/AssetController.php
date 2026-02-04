<?php

namespace App\Http\Controllers\Assets;

use App\Models\Asset;
use App\Models\User;
use App\Models\Location;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Support\DepartmentContext;


class AssetController extends Controller
{
    /**
     * List all assets
     */
    public function index()
{
    $departmentId = DepartmentContext::id();

    return view('assets.index', [
        'assets' => Asset::with(['assigned', 'location'])
            ->where('department_id', $departmentId)
            ->get()
    ]);
}


    /**
     * Show single asset
     */
    public function show(Asset $asset)
{
    $departmentId = DepartmentContext::id();

    // Safety: asset must belong to current department
    if ($asset->department_id !== $departmentId) {
        abort(403);
    }

   

    return view('assets.show', [
    'asset' => $asset,

    // Users in this department
    'users' => User::where('department_id', $departmentId)->get(),

    // All locations (server room, conference room, etc.)
    'locations' => Location::where('department_id', $departmentId)->get(),

    // Other assets in same department
    'assets' => Asset::where('department_id', $departmentId)
        ->where('id', '!=', $asset->id)
        ->get(),
]);

    
}


    /**
     * Checkout asset (user / location / asset)
     */
    public function checkout(Request $request, Asset $asset)
{
    $departmentId = DepartmentContext::id();

    // Asset must belong to current department
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


    /**
     * Check in asset
     */
    public function checkin(Request $request, Asset $asset)
{
    $departmentId = DepartmentContext::id();

    // Safety: asset must belong to current department
    if ($asset->department_id !== $departmentId) {
        abort(403);
    }

    $request->validate([
        'note' => 'nullable|string',
    ]);

    // Perform check-in (no location)
    $asset->checkIn(note: $request->note);

    return back()->with('success', 'Asset checked in successfully');
}

}
