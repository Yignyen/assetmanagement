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
            'delete'   => $this->bulkDelete($assets),
            'checkout' => $this->bulkCheckoutRedirect($assets),
            'edit'     => $this->bulkEditRedirect($assets),
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
        $assigned = $assets->whereNotNull('assigned_to');

        if ($assigned->isNotEmpty()) {
            return back()->with('error',
                'Some selected assets are assigned. Please check-in before deleting.'
            );
        }

        DB::transaction(function () use ($assets) {
            foreach ($assets as $asset) {
                $asset->delete();
            }
        });

        return back()->with('success', 'Selected assets deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Checkout Redirect
    |--------------------------------------------------------------------------
    */

    protected function bulkCheckoutRedirect($assets) //method is called from above  $this->bulkCheckoutRedirect($assets);
    {
        session()->put('bulk_checkout_ids', $assets->pluck('id')->toArray());  // extract only Id filed from each models, takes only number id and make to arrray [3,32,4], 
                                                                            // and temp saved inside session.(session are use as rediect page will lost the id and session used for reminder)
        return redirect()->route('assets.bulk.checkout.form');      
    }
    /*
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
        'ids' => 'required|array|min:1',
        'status_id' => 'nullable|exists:status_labels,id',  //If provided → must exist in column status_labels table
        'label' => 'nullable|string|max:255',


        'model_id' => 'nullable|exists:models,id',//must exsits in model-id
    ]);

    $departmentId = DepartmentContext::id();

    $assets = Asset::where('department_id', $departmentId)
        ->whereIn('id', $request->ids)
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
    $ids = session()->get('bulk_checkout_ids');

    if (!$ids || count($ids) === 0) {
        return redirect()->route('assets.index')
            ->with('error', 'No assets selected.');
    }

    $departmentId = DepartmentContext::id();

    $assets = Asset::where('department_id', $departmentId)
        ->whereIn('id', $ids)
        ->get();

    if ($assets->isEmpty()) {
        return redirect()->route('assets.index')
            ->with('error', 'Invalid asset selection.');
    }

    if ($assets->whereNotNull('assigned_to')->isNotEmpty()) {
        return redirect()->route('assets.index')
            ->with('error', 'Some assets are already assigned.');
    }

    return view('assets.bulk-checkout', [
        'assets'    => $assets,
        'users'     => User::where('department_id', $departmentId)->get(),
        'locations' => Location::where('department_id', $departmentId)->get(),
        'allAssets' => Asset::where('department_id', $departmentId)->get(),
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
        'ids'              => 'required|array|min:1',
        'checkout_to_type' => 'required|in:user,location,asset',
        'checkout_to_id'   => 'required|integer',
        'note'             => 'nullable|string',
    ]);

    $departmentId = DepartmentContext::id();

    $assets = Asset::where('department_id', $departmentId)
        ->whereIn('id', $request->ids)
        ->get();

    if ($assets->isEmpty()) {
        return redirect()->route('assets.index')
            ->with('error', 'No valid assets selected.');
    }

    // Prevent already assigned assets
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
}
