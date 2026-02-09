<?php

namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\User;
use App\Models\Location;
use App\Support\DepartmentContext;
use Illuminate\Http\Request;
use App\Models\AssetModel;
use App\Services\AssetTagService;
use Illuminate\Validation\Rule;

class AssetController extends Controller
{
    /**
     * List all assets (department scoped)
     */
    public function index(Request $request)
{
    $departmentId = DepartmentContext::id();

    $query = Asset::with(['assigned', 'location', 'model.category'])
        ->where('department_id', $departmentId);

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('search')) {
        $search = $request->search;
//checks in asset tag, asset serial no ,asset label, asset model
        $query->where(function ($q) use ($search) {
            $q->where('label', 'like', "%{$search}%")
              ->orWhere('asset_tag', 'like', "%{$search}%")
              ->orWhere('serial_no', 'like', "%{$search}%")
              ->orWhereHas('model', fn ($q) =>
                    $q->where('name', 'like', "%{$search}%"))
              ->orWhereHas('model.category', fn ($q) =>
                    $q->where('name', 'like', "%{$search}%"));
        });
    }

    $assets = $query->latest()->get();

    return view('assets.index', compact('assets'));
}


    /**
     * Show create asset form
     */
    

    public function create()
    {
        return view('assets.create', [
            'models' => AssetModel::with('category')
                ->orderBy('name')
                ->get(),

                /* 'assetTag' => AssetTagService::generate(), */ //generates asset tag in controller , prevents multiple asset generator when load in new new pages.
            ]);
        }


    /**
     * Store new asset
     */
    public function store(Request $request)
{
    $departmentId = DepartmentContext::id();

    $request->validate([
        'name'      => 'nullable|string|max:255',
        'serial_no' => 'required|string|max:255',
        'model_id'  => 'required|string|max:255',

        'asset_tag' => ['required','string',
                        Rule::unique('assets')->where('department_id', $departmentId),
        ],
    ]);

    Asset::create([
        'name'          => $request->name, // optional
        'model_id'      => $request->model_id,
        'serial_no'     => $request->serial_no,

        // 👇 THIS is the key line
        'asset_tag'     => $request->asset_tag
                            ?: AssetTagService::generate(),

        'department_id' => $departmentId,
        'status'        => 'available',
        'assigned_to'   => null,
        'assigned_type' => null,
        'location_id'   => null,
    ]);

    return redirect()
        ->route('assets.index')
        ->with('success', 'Asset created successfully');
}

    /**
     * Show single asset
     */
    public function show(Asset $asset)
    {
        $departmentId = DepartmentContext::id();

        if ($asset->department_id !== $departmentId) {
            abort(403);
        }

        return view('assets.show', [
            'asset'     => $asset,
            'users'     => User::where('department_id', $departmentId)->get(),
            'locations' => Location::where('department_id', $departmentId)->get(),
            'assets'    => Asset::where('department_id', $departmentId)
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

        if ($asset->department_id !== $departmentId) {
            abort(403);
        }

        $request->validate([
            'note' => 'nullable|string',
            'redirect_to' => 'nullable|string',
        ]);

        $asset->checkIn($request->note);

        // ✅ Case 2: came from Edit intent
        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)
                ->with('success', 'Asset unassigned. You can now edit it.');
        }

        // ✅ Case 1: normal unassign
        return redirect()
            ->route('assets.index')
            ->with('success', 'Asset unassigned successfully.');

    }

    /**
     * Delete asset (soft delete)
     */
    public function destroy(Asset $asset)
    {
        $departmentId = DepartmentContext::id();

        if ($asset->department_id !== $departmentId) {
            abort(403);
        }

        if ($asset->assigned_to !== null) {
            return back()->with('error', 'Check in asset before deleting');
        }

        $asset->delete();

        return redirect()
            ->route('assets.index')
            ->with('success', 'Asset deleted successfully');
    }


//for edit 
    public function edit(Asset $asset)
{
    $departmentId = DepartmentContext::id();

    if ($asset->department_id !== $departmentId) {
        abort(403);
    }

    if ($asset->assigned_to !== null) {
        return redirect()
            ->route('assets.show', $asset)
            ->with('error', 'Unassign asset before editing')
            ->with('redirect_after_checkin', route('assets.edit', $asset));
    }

    return view('assets.edit', [
        'asset'  => $asset,
        'models' => AssetModel::with('category')
            ->orderBy('name')
            ->get(),
    ]);
}



    public function update(Request $request, Asset $asset)
{
    $departmentId = DepartmentContext::id();

    if ($asset->department_id !== $departmentId) {
        abort(403);
    }

    if ($asset->assigned_to !== null) {
        return back()->with('error', 'Unassign asset before updating');
    }

    $request->validate([
        'name'      => 'nullable|string|max:255',
        'serial_no' => 'required|string|max:255',
        'model_id'  => 'required|integer|exists:models,id',
    ]);

    $asset->update([
        'name'      => $request->name,
        'serial_no' => $request->serial_no,
        'model_id'  => $request->model_id,
    ]);

    return redirect()
        ->route('assets.show', $asset)
        ->with('success', 'Asset updated successfully');
}

}
