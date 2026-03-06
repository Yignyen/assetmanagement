<?php

namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\User;
use App\Models\Location;
use App\Support\DepartmentContext;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\AssetModel;
use App\Models\StatusLabel;
use Illuminate\Http\RedirectResponse;
use App\Services\AssetTagService;
use Illuminate\Validation\Rule;

class AssetController extends Controller
{
    /**
     * List all assets
     */
    public function index(Request $request)
{
    $departmentId = DepartmentContext::id();

    $baseQuery = Asset::with(['status', 'assigned', 'location', 'model.category'])
        ->where('department_id', $departmentId);

    $query = clone $baseQuery;

    // ===============================
    // LIFECYCLE FILTER
    // ===============================
    if ($request->filled('type')) {

        match ($request->type) {

            'rtd' => $query
                ->whereNull('assigned_to')
                ->whereHas('status', fn($q) => $q->deployable()),

            'deployed' => $query->whereNotNull('assigned_to'),

            'pending' => $query->whereHas('status', fn($q) => $q->pending()),

            'undeployable' => $query->whereHas('status', fn($q) => $q->undeployable()),

            'archived' => $query->whereHas('status', fn($q) => $q->archived()),
        };
    }

    // ===============================
    // SEARCH FILTER
    // ===============================
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {

            $q->where('label', 'like', "%{$search}%")
              ->orWhere('asset_tag', 'like', "%{$search}%")
              ->orWhere('serial_no', 'like', "%{$search}%")

              ->orWhereHas('model', function ($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%");
              })

              ->orWhereHas('model.category', function ($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%");
              })

              ->orWhereHasMorph(
                  'assigned',
                  [\App\Models\User::class],
                  fn($q) => $q->where('name', 'like', "%{$search}%")
              )

              ->orWhereHasMorph(
                  'assigned',
                  [\App\Models\Location::class],
                  fn($q) => $q->where('name', 'like', "%{$search}%")
              );
        });
    }

    // ===============================
    // PAGINATION
    // ===============================
    $assets = $query->latest()->paginate(15);

    // ===============================
    // SIDEBAR COUNTS
    // ===============================
    $counts = [
        'all' => (clone $baseQuery)->count(),

        'rtd' => (clone $baseQuery)
            ->whereNull('assigned_to')
            ->whereHas('status', fn($q) => $q->deployable())
            ->count(),

        'deployed' => (clone $baseQuery)
            ->whereNotNull('assigned_to')
            ->count(),

        'pending' => (clone $baseQuery)
            ->whereHas('status', fn($q) => $q->pending())
            ->count(),

        'undeployable' => (clone $baseQuery)
            ->whereHas('status', fn($q) => $q->undeployable())
            ->count(),

        'archived' => (clone $baseQuery)
            ->whereHas('status', fn($q) => $q->archived())
            ->count(),
    ];

    return view('assets.index', compact('assets', 'counts'));
}

    
    

    /**
     * Create asset form
     */
  public function create()
{
    return view('assets.create', [
        'categories' => Category::orderBy('name')->get(),

        'models' => AssetModel::with('category')
            ->orderBy('name')
            ->get(),

        'manufacturers' => \App\Models\Manufacturer::orderBy('name')->get(),
    ]);
}
    /**
     * Store asset
     */
    public function store(Request $request)
    {
        $departmentId = DepartmentContext::id();
        $defaultStatus = \App\Models\StatusLabel::where('default_label', true)->firstOrFail();

        $request->validate([
            'name'      => 'nullable|string|max:255',
            'serial_no' => 'required|string|max:255',
            'model_id'  => 'required|integer|exists:models,id',
            'asset_tag' => [
                'required',
                'string',
                Rule::unique('assets')->where('department_id', $departmentId),
            ],
        ]);

        Asset::create([
            'name'          => $request->name,
            'model_id'      => $request->model_id,
            'serial_no'     => $request->serial_no,
            'asset_tag'     => $request->asset_tag ?: AssetTagService::generate(),
            'department_id' => $departmentId,
            'status_id'     => $defaultStatus->id,
        ]);

        return redirect()
            ->route('assets.index')
            ->with('success', 'Asset created successfully');
    }

    /**
     * Show asset
     */
    public function show(Asset $asset)
{
    if ($asset->department_id !== DepartmentContext::id()) {
        abort(403);
    }

    $departmentId = DepartmentContext::id();

    $users = User::where('department_id', $departmentId)->get();

    $locations = Location::where('department_id', $departmentId)->get();

    $assets = Asset::where('department_id', $departmentId)
        ->where('id', '!=', $asset->id) // prevent assigning to itself
        ->get();

    $statuses = StatusLabel::all();

    return view('assets.show', compact(
        'asset',
        'users',
        'locations',
        'assets',
        'statuses'
    ));
}

    /**
     * Edit asset
     */
    public function edit(Asset $asset)
    {
        if ($asset->department_id !== DepartmentContext::id()) {
            abort(403);
        }

        
        $models = AssetModel::with('category')->orderBy('name')->get();
                                            
        

        $statuses = StatusLabel::all();

        return view('assets.edit', [
            'asset'     => $asset,
            'models'    => $models,
            'statuses'  => $statuses,

        ]);
    }

  public function update(Request $request, Asset $asset)
{
    if ($asset->department_id !== DepartmentContext::id()) {
        abort(403);
    }

    $request->validate([
        'name'      => 'nullable|string|max:255',
        'serial_no' => 'required|string|max:255',
        'model_id'  => 'required|integer|exists:models,id',
        'status_id' => 'required|exists:status_labels,id',
    ]);

    $updatedStatus = StatusLabel::findOrFail($request->status_id);

    $statusChanged = $asset->status_id != $updatedStatus->id;

    /*
    |--------------------------------------------------------------------------
    | 🔥 TRUE SNIPE-STYLE LOGIC
    |--------------------------------------------------------------------------
    | If status becomes non-deployable
    | AND asset is assigned
    | → auto check-in using model method
    */

    if ($statusChanged) {

        $isNonDeployable =
            !$updatedStatus->deployable &&
            !$updatedStatus->pending;

        if ($isNonDeployable && $asset->assigned_to !== null) {

            // This handles unassignment properly
            $asset->checkIn(
                note: 'Auto check-in due to status change',
                statusId: $updatedStatus->id
            );

        } else {
            $asset->status_id = $updatedStatus->id;
        }
    }

    // Update normal fields
    $asset->name      = $request->name;
    $asset->serial_no = $request->serial_no;
    $asset->model_id  = $request->model_id;

    $asset->save();

    return redirect()
        ->route('assets.show', $asset)
        ->with('success', 'Asset updated successfully.');
}


    /**
     * Delete asset
     */
    public function destroy(Asset $asset)
    {
        if ($asset->department_id !== DepartmentContext::id()) {
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

     
/**
 * Show deleted (soft deleted) assets
 */
public function deleted()
{
    $departmentId = DepartmentContext::id();

    $assets_deleted = Asset::onlyTrashed()
        ->with(['status', 'assigned', 'location', 'model.category'])
        ->where('department_id', $departmentId)
        ->latest()
        ->paginate(15);

    return view('assets.bulk-deleted-page', compact('assets_deleted'));
}
    


public function restore(Asset $asset): RedirectResponse
{
    // Include trashed models
    $asset = Asset::withTrashed()->findOrFail($asset->id);

    if ($asset->department_id !== DepartmentContext::id()) {
        abort(403);
    }

    if (!$asset->trashed()) {
        return redirect()
            ->route('assets.deleted')
            ->with('error', 'Asset is not deleted.');
    }

    $asset->restore();

    return redirect()
        ->route('assets.deleted')
        ->with('success', 'Asset restored successfully.');
}



//for mdoel by categories

public function modelsByCategory(Request $request)
{
    $models = \App\Models\AssetModel::with('manufacturer')
        ->where('category_id', $request->category_id)
        ->whereNull('deleted_at')
        ->orderBy('name')
        ->get();

    return response()->json(
        $models->map(function ($model) {

            $brand = $model->manufacturer
                ? $model->manufacturer->name
                : '';

            return [
                'id' => $model->id,
                'name' => $brand . ' — ' . $model->name,
                'manufacturer_id' => $model->manufacturer_id
            ];
        })
    );
}

}
