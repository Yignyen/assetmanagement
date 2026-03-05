<?php

namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\StatusLabel;
use App\Support\DepartmentContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AssetModel;
use App\Models\User;
use App\Models\Location;
use Illuminate\Support\Facades\Redirect;

class BulkAssetsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Main Bulk Handler
    |--------------------------------------------------------------------------
    */

    public function handle(Request $request)
    {
        $request->validate([ //auto rediect bakc with error
            'bulk_action' => 'required|string',
            'ids' => 'required|array|min:1', //selected IDS, but atleast one
        ]);

        $departmentId = DepartmentContext::id();

        $assets = Asset::where('department_id', $departmentId)
            ->whereIn('id', $request->ids)
            ->get(); //executes query and retunrs collection of assetmodels

        if ($assets->isEmpty()) { //incase if emptyretunr witth mesage error
            return back()->with('error', 'No valid assets selected.');
        }

        return match ($request->bulk_action) { //modern version of switch-case 
            'delete' => $this->bulkDeleteRedirect($assets, $request),
            'checkout' => $this->bulkCheckoutRedirect($assets,$request),
            'edit'     => $this->bulkEditRedirect($assets),
           /*  'restore' => $this->bulkRestoreRedirect($assets, $request), */
            default    => back()->with('error', 'Invalid bulk action.'),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Delete
    |--------------------------------------------------------------------------
    */

   protected function bulkDelete($assets)
{
    // Find assigned assets
    $assignedAssets = $assets->whereNotNull('assigned_to');

    if ($assignedAssets->isNotEmpty()) {

        $blockedTags = $assignedAssets->pluck('asset_tag')->toArray();

        return back()
            ->with('delete_blocked', $blockedTags)
            ->with('error', 'Some assets are currently assigned and cannot be deleted.');
    }

    DB::transaction(function () use ($assets) {
        foreach ($assets as $asset) {
            $asset->delete(); // Soft delete
        }
    });

    return back()->with('success', 'Selected assets deleted successfully.');
}

//redirect for bulk delete

protected function bulkDeleteRedirect($assets, Request $request)
{
    // Store selected IDs in session
    $request->session()->put(
        'bulk_delete_ids',
        $assets->pluck('id')->toArray()
    );

    return redirect()->route('assets.bulk.delete.confirm');
}










//bulk delete confirmation

public function bulkDeleteConfirm()
{
    $departmentId = DepartmentContext::id();

    $ids = session('bulk_delete_ids', []);

    if (empty($ids)) {
        return redirect()->route('assets.index')
            ->with('error', 'No assets selected.');
    }

    $assets = Asset::with(['location', 'assigned'])
        ->where('department_id', $departmentId)
        ->whereIn('id', $ids)
        ->get();

    if ($assets->isEmpty()) {
        return redirect()->route('assets.index')
            ->with('error', 'Invalid asset selection.');
    }

    return view('assets.bulk-delete', compact('assets'));
}

//final bulk delete process

public function bulkDeleteProcess()
{
    $departmentId = DepartmentContext::id();

    $ids = session()->pull('bulk_delete_ids', []);

    if (empty($ids)) {
        return redirect()->route('assets.index')
            ->with('error', 'No assets selected.');
    }

    $assets = Asset::where('department_id', $departmentId)
        ->whereIn('id', $ids)
        ->get();

    if ($assets->isEmpty()) {
        return redirect()->route('assets.index')
            ->with('error', 'No valid assets found.');
    }

    // Split assigned vs deletable
    $assignedAssets = $assets->whereNotNull('assigned_to');
    $deletableAssets = $assets->whereNull('assigned_to');

    DB::transaction(function () use ($deletableAssets) {
        foreach ($deletableAssets as $asset) {
            $asset->delete(); // Soft delete
        }
    });

    // Prepare messages
    if ($assignedAssets->isNotEmpty() && $deletableAssets->isNotEmpty()) {

        $blockedTags = $assignedAssets->pluck('asset_tag')->implode(', ');

        return redirect()->route('assets.index')
            ->with('error',
                'Asset Tags: ' . $blockedTags .
                ' are currently checked out. Check in these devices before deletion.'
            )
            ->with('success',
                $deletableAssets->count() . ' assets were deleted successfully.'
            );
    }

    if ($assignedAssets->isNotEmpty()) {

        $blockedTags = $assignedAssets->pluck('asset_tag')->implode(', ');

        return redirect()->route('assets.index')
            ->with('error',
                'Asset Tags: ' . $blockedTags .
                ' are currently checked out. Check in these devices before deletion.'
            );
    }

    return redirect()->route('assets.index')
        ->with('success', 'Assets deleted successfully.');
}
    /*
    |--------------------------------------------------------------------------
    | Bulk Checkout Redirect
    |--------------------------------------------------------------------------
    */

    /* protected function bulkCheckoutRedirect($assets, Request $request) //method is called from above  $this->bulkCheckoutRedirect($assets);
    {
        request()->session()->flashInput([  
        'selected_assets' => $assets->pluck('id')->toArray()
    ]);  // extract only Id filed from each models, takes only number id and make to arrray [3,32,4], 
                                                                            // and temp saved inside session.(session are use as rediect page will lost the id and session used for reminder)
        return redirect()->route('assets.bulk.checkout.form');      
    } */

        /* protected function bulkCheckoutRedirect($assets, Request $request)
{
    // Flash selected asset IDs for next request only
    // This stores them inside session under _old_input
    $request->session()->flash('_old_input', [
    'selected_assets' => $assets->pluck('id')->toArray()
]);

    return redirect()->route('assets.bulk.checkout.form');
} */
protected function bulkCheckoutRedirect($assets, Request $request)
{
    // Split invalid (undeployable OR already assigned)
    $invalid = $assets->filter(function ($asset) {
        return $asset->status?->deployable == 0 || !is_null($asset->assigned_to);
    });

    $valid = $assets->diff($invalid);

    // Always go to checkout page
    $request->session()->flash('_old_input', [
        'selected_assets' => $valid->pluck('id')->values()->toArray()
    ]);

    // If some were removed → show warning
    if ($invalid->isNotEmpty()) {
        $removedTags = $invalid->pluck('asset_tag')->toArray();
        $remainingTags = $valid->pluck('asset_tag')->toArray();

        return redirect()
                    ->route('assets.bulk.checkout.form')
                    ->with('removed_assets', $removedTags)
                    ->with('remaining_assets', $remainingTags)
                    ->with('warning', true);
    }

    return redirect()->route('assets.bulk.checkout.form');
}  /*
    |--------------------------------------------------------------------------
    | Bulk Edit Redirect
    |--------------------------------------------------------------------------
    */

    protected function bulkEditRedirect($assets)
    {
        session()->put('bulk_edit_ids', $assets->pluck('id')->toArray());   //same as above
        return redirect()->route('assets.bulk.edit.form');
    }

    /*
    |--------------------------------------------------------------------------
    | Show Bulk Edit Form
    |--------------------------------------------------------------------------
    */

    public function editForm() //no parameters, relies on session data
    {
        $ids = session()->get('bulk_edit_ids'); //Retrieves the array of asset IDs that were stored earlier
            //check-1
        if (!$ids || count($ids) === 0) {   //checks, session key null or  empty array ,eithe ris true m user didnt select assets.
            return redirect()->route('assets.index')
                ->with('error', 'No assets selected.');
        }

        $departmentId = DepartmentContext::id();

        $assets = Asset::where('department_id', $departmentId)
            ->whereIn('id', $ids)
            ->get();
            //check-2
        if ($assets->isEmpty()) { //handles case like - id manipulated, asset deleted before fomr loaded, user switched departmemt s
            return redirect()->route('assets.index')
                ->with('error', 'Invalid asset selection.');
        }

        $statuses = StatusLabel::all();
        $models =AssetModel::all();

        return view('assets.bulk-edit', compact('assets', 'statuses','models'));  //comapct , creates array like , and inside blade we can use those 3
    }

    /*
    |--------------------------------------------------------------------------
    | Snipe-IT Style Status Transition Logic
    |--------------------------------------------------------------------------
    */
    //This method requires: an Asset object ,a StatusLabel object And it must return a boolean (true or false).

    protected function canChangeStatus(Asset $asset, StatusLabel $newStatus): bool  //called type method signature, parameter type hints and retunr type declaration
    {
        $currentStatus = $asset->status;

        $isUnassigned = is_null($asset->assigned_to);  //if asset is not asssigned to anyone then retunr true.

        $bothDeployable =
            $newStatus->deployable == 1 && //checks new status is deployable and currentsatus is deployable and blocks if assets is assigned
            $currentStatus?->deployable == 1; //if crrent status is null, thne dont throw error, prevents crash (?)

        $isPending = $newStatus->pending == 1; 

        return $isUnassigned || $bothDeployable || $isPending; //retunr true if any condition is true
    }

    /*
    |--------------------------------------------------------------------------
    | Process Bulk Update
    |--------------------------------------------------------------------------
    */

    public function update(Request $request)
{
    $request->validate([
        'selected_assets' => 'required|array|min:1',
        'status_id' => 'nullable|exists:status_labels,id',  //If provided → must exist in column status_labels table
        'label' => 'nullable|string|max:255',


        'model_id' => 'nullable|exists:models,id',//must exsits in model-id
    ]);

    $departmentId = DepartmentContext::id();

    $assets = Asset::where('department_id', $departmentId)
        ->whereIn('id', $request->selected_assets)
        ->get();

    if ($assets->isEmpty()) { //safety cheks
        return redirect()->route('assets.index')
            ->with('error', 'No valid assets selected.');
    }

    $skippedStatus = []; //storage for asste tags where status blocled

    DB::transaction(function () use ($assets, $request, &$skippedStatus) { // if something fials, all uodate roll back

        foreach ($assets as $asset) {

            $data = [];

            // ================================
            // STATUS UPDATE (SNIPE STYLE)
            // ================================
            if ($request->filled('status_id')) {

                $newStatus = StatusLabel::find($request->status_id);  //feteches new status model from DB

                if ($newStatus && $this->canChangeStatus($asset, $newStatus)) { //ceheks;- status exist and business rule allows transition
                    $data['status_id'] = $newStatus->id; //if allowed then chnage
                } else {
                    // Just record skipped — do NOT break update
                    $skippedStatus[] = $asset->asset_tag;
                }
            }

            // ================================
                // MODEL UPDATE
            // ================================
            if ($request->filled('model_id')) {
                $data['model_id'] = $request->model_id;
                }

            // ================================
            // LABEL UPDATE
            // ================================
            if ($request->has('clear_label')) {
                $data['label'] = null;
            }
            elseif ($request->filled('label')) {
                $data['label'] = $request->label;
            }

            if (!empty($data)) {
                $asset->update($data); //if sleected nothing no uodate runs
            }
        }
    });


    

    // ================================
    // AFTER TRANSACTION
    // ================================
    if (!empty($skippedStatus)) {
        return redirect()->route('assets.index')
            ->with('warning',
                'Status not changed for: ' . implode(', ', $skippedStatus)
            );
    }

    return redirect()->route('assets.index')
        ->with('success', 'Bulk update successful.');
}


/*
    |--------------------------------------------------------------------------
    | bulk CheckOut form
    |--------------------------------------------------------------------------
    */

public function checkoutForm()
{
    $departmentId = DepartmentContext::id();

    $selectedIds = old('selected_assets', []);

    // If user did not come from bulk flow at all
    if (!is_array($selectedIds)) {
        return redirect()->route('assets.index')
            ->with('error', 'No assets selected.');
    }

    // Load assets if any IDs exist
    $assets = collect();

    if (!empty($selectedIds)) {
        $assets = Asset::with('status')
            ->where('department_id', $departmentId)
            ->whereIn('id', $selectedIds)
            ->get();
    }

    // IMPORTANT:
    // We do NOT block empty collection anymore.
    // That allows page to open even if all were removed.

    return view('assets.bulk-checkout', [
        'assets'    => $assets,
        'users'     => User::where('department_id', $departmentId)->get(),
        'locations' => Location::where('department_id', $departmentId)->get(),
        'allAssets' => Asset::where('department_id', $departmentId)
                            ->deployable()
                            ->whereNull('assigned_to')
                            ->get(),
    ]);
}

/*
    |--------------------------------------------------------------------------
    | bulk CheckOut process
    |--------------------------------------------------------------------------
    */





public function checkoutProcess(Request $request)
{
    $request->validate([
        /* 'ids'              => 'required|array|min:1', */
        'selected_assets' => 'required|array|min:1',
        'checkout_to_type' => 'required|in:user,location,asset',
        'checkout_to_id'   => 'required|integer',
        'note'             => 'nullable|string',
    ]);

    $departmentId = DepartmentContext::id();

    $assets = Asset::where('department_id', $departmentId)
        ->whereIn('id', $request->selected_assets)
        ->get();

    if ($assets->isEmpty()) {
        return redirect()->route('assets.index')
            ->with('error', 'No valid assets selected.');
    }

    // Prevent already assigned assets
   /*  if ($assets->whereNotNull('assigned_to')->isNotEmpty()) {
        return redirect()->route('assets.index')
            ->with('error', 'Some assets are already assigned.');
    } */

    // Prevent undeployable
    if ($assets->filter(fn($a) => $a->status?->deployable == 0)->isNotEmpty()) {
        return redirect()->route('assets.index')
            ->with('error', 'Some selected assets are not deployable.');
    }

    // Prevent assigned
    if ($assets->whereNotNull('assigned_to')->isNotEmpty()) {
        return redirect()->route('assets.index')
            ->with('error', 'Some assets are already assigned.');
    }


    DB::transaction(function () use ($assets, $request, $departmentId) {

        foreach ($assets as $asset) {

            match ($request->checkout_to_type) {

                'user' => $asset->checkOutToUser(
                    \App\Models\User::where('department_id', $departmentId)
                        ->findOrFail($request->checkout_to_id),
                    $request->note
                ),

                'location' => $asset->checkOutToLocation(
                    \App\Models\Location::where('department_id', $departmentId)
                        ->findOrFail($request->checkout_to_id),
                    $request->note
                ),

                'asset' => $asset->checkOutToAsset(
                    Asset::where('department_id', $departmentId)
                        ->findOrFail($request->checkout_to_id),
                    $request->note
                ),
            };
        }
    });

    return redirect()->route('assets.index')
        ->with('success', 'Bulk checkout successful.');
} 



/*
    |--------------------------------------------------------------------------
    | bulk Restore
    |--------------------------------------------------------------------------
    */

    /* bulk restore rediect */


/* form for bulk  */



   public function restoreForm()
{
    $departmentId = DepartmentContext::id();

    $assets = Asset::onlyTrashed()
        ->where('department_id', $departmentId)
        ->latest()
        ->get();

    return view('assets.bulk-restore', compact('assets'));
}


public function restoreProcess(Request $request)
{
    $request->validate([
    'selected_assets' => 'required|array|min:1',
]);

    Asset::onlyTrashed()
        ->whereIn('id', $request->selected_assets)
        ->where('department_id', DepartmentContext::id())
        ->restore();

    return redirect()
        ->route('assets.deleted')
        ->with('success', 'Selected assets restored successfully.');
}

public function ajaxAssets(Request $request)
{
    $departmentId = DepartmentContext::id();

    $query = Asset::with(['status','model'])
        ->where('department_id', $departmentId)
        ->deployable()
        ->whereNull('assigned_to');

    if ($request->filled('q')) {
        $query->where(function ($q) use ($request) {
            $q->where('asset_tag', 'like', "%{$request->q}%")
              ->orWhere('serial_no', 'like', "%{$request->q}%")
              ->orWhere('name', 'like', "%{$request->q}%");
        });
    }

    return response()->json([
        'results' => $query->limit(20)->get()->map(function ($asset) {
            return [
                'id'   => $asset->id,
                'text' => $asset->asset_tag . ' - ' . ($asset->model?->name ?? ''),
            ];
        })
    ]);
}

//select2 with trashed only for bulknrestore


public function ajaxDeletedAssets(Request $request)
{
    $departmentId = DepartmentContext::id();

    $query = Asset::onlyTrashed() // 👈 IMPORTANT
        ->where('department_id', $departmentId);

    if ($request->filled('q')) {
        $query->where(function ($q) use ($request) {
            $q->where('asset_tag', 'like', "%{$request->q}%")
              ->orWhere('serial_no', 'like', "%{$request->q}%")
              ->orWhere('name', 'like', "%{$request->q}%");
        });
    }

    return response()->json([
        'results' => $query->limit(20)->get()->map(function ($asset) {
            return [
                'id' => $asset->id,
                'text' => $asset->asset_tag . ' - ' . ($asset->name ?? ''),
            ];
        })
    ]);
}
}


