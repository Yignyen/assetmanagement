<?php

namespace App\Http\Controllers\Assets;

use App\Models\Asset;
use App\Models\User;
use App\Models\Location;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * List all assets
     */
    public function index()
    {
        return view('assets.index', [
            'assets' => Asset::with(['assigned', 'location'])->get()
        ]);
    }

    /**
     * Show single asset
     */
    public function show(Asset $asset)
    {
        return view('assets.show', [
            'asset'     => $asset,
            'users'     => User::all(),
            'locations' => Location::whereNotNull('parent_id')->get(), // rooms only
            'assets'    => Asset::where('id', '!=', $asset->id)->get(),
            'departments' => Location::whereNull('parent_id')->get(),
        ]);
    }

    /**
     * Checkout asset (user / location / asset)
     */
    public function checkout(Request $request, Asset $asset)
    {
        $request->validate([
            'checkout_to_type' => 'required|in:user,location,asset',
            'checkout_to_id'   => 'required|integer',
            'note'             => 'nullable|string',
        ]);

        match ($request->checkout_to_type) {
            'user' => $asset->checkOutToUser(
                User::findOrFail($request->checkout_to_id),
                $request->note
            ),

            'location' => $asset->checkOutToLocation(
                Location::findOrFail($request->checkout_to_id),
                $request->note
            ),

            'asset' => $asset->checkOutToAsset(
                Asset::findOrFail($request->checkout_to_id),
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
        $request->validate([
            'department_id' => 'required|exists:locations,id',
            'note'          => 'nullable|string',
        ]);

        $asset->checkIn(
            departmentId: $request->department_id,
            note: $request->note
        );

        return back()->with('success', 'Asset checked in successfully');
    }
}
