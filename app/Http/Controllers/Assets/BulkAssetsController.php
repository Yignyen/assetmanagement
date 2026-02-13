<?php

namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\StatusLabel;
use App\Support\DepartmentContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BulkAssetsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Main Bulk Handler
    |--------------------------------------------------------------------------
    */

    public function handle(Request $request)
    {
        $request->validate([
            'bulk_action' => 'required|string',
            'ids' => 'required|array|min:1',
        ]);

        $departmentId = DepartmentContext::id();

        $assets = Asset::where('department_id', $departmentId)
            ->whereIn('id', $request->ids)
            ->get();

        if ($assets->isEmpty()) {
            return back()->with('error', 'No valid assets selected.');
        }

        return match ($request->bulk_action) {
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

    protected function bulkCheckoutRedirect($assets)
    {
        session()->put('bulk_checkout_ids', $assets->pluck('id')->toArray());
        return redirect()->route('assets.bulk.checkout.form');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Edit Redirect
    |--------------------------------------------------------------------------
    */

    protected function bulkEditRedirect($assets)
    {
        session()->put('bulk_edit_ids', $assets->pluck('id')->toArray());
        return redirect()->route('assets.bulk.edit.form');
    }

    /*
    |--------------------------------------------------------------------------
    | Show Bulk Edit Form
    |--------------------------------------------------------------------------
    */

    public function editForm()
    {
        $ids = session()->get('bulk_edit_ids');

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

        $statuses = StatusLabel::all();

        return view('assets.bulk-edit', compact('assets', 'statuses'));
    }

    /*
    |--------------------------------------------------------------------------
    | Snipe-IT Style Status Transition Logic
    |--------------------------------------------------------------------------
    */

    protected function canChangeStatus(Asset $asset, StatusLabel $newStatus): bool
    {
        $currentStatus = $asset->status;

        $isUnassigned = is_null($asset->assigned_to);

        $bothDeployable =
            $newStatus->deployable == 1 &&
            $currentStatus?->deployable == 1;

        $isPending = $newStatus->pending == 1;

        return $isUnassigned || $bothDeployable || $isPending;
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
        'status_id' => 'nullable|exists:status_labels,id',
        'label' => 'nullable|string|max:255'
    ]);

    $departmentId = DepartmentContext::id();

    $assets = Asset::where('department_id', $departmentId)
        ->whereIn('id', $request->ids)
        ->get();

    if ($assets->isEmpty()) {
        return redirect()->route('assets.index')
            ->with('error', 'No valid assets selected.');
    }

    $skippedStatus = [];

    DB::transaction(function () use ($assets, $request, &$skippedStatus) {

        foreach ($assets as $asset) {

            $data = [];

            // ================================
            // STATUS UPDATE (SNIPE STYLE)
            // ================================
            if ($request->filled('status_id')) {

                $newStatus = StatusLabel::find($request->status_id);

                if ($newStatus && $this->canChangeStatus($asset, $newStatus)) {
                    $data['status_id'] = $newStatus->id;
                } else {
                    // Just record skipped — do NOT break update
                    $skippedStatus[] = $asset->asset_tag;
                }
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
                $asset->update($data);
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

}
