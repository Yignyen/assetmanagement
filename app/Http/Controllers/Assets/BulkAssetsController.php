<?php

namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Support\DepartmentContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StatusLabel;


class BulkAssetsController extends Controller
{
    /**
     * Main bulk handler
     * This works like Snipe-IT:
     * - Receives selected IDs
     * - Checks action
     * - Redirects or processes
     */
    public function handle(Request $request)
    {
        $request->validate([
            'bulk_action' => 'required|string',
            'ids' => 'required|array|min:1',
        ]);

        $departmentId = DepartmentContext::id();

        // Only allow assets from current department
        $assets = Asset::where('department_id', $departmentId)
            ->whereIn('id', $request->ids)
            ->get();

        if ($assets->isEmpty()) {
            return back()->with('error', 'No valid assets selected.');
        }

        return match ($request->bulk_action) {

            'delete'      => $this->bulkDelete($assets),

            'checkout'    => $this->bulkCheckoutRedirect($assets),

            'edit'        => $this->bulkEditRedirect($assets),

            default       => back()->with('error', 'Invalid bulk action.')
        };
    }

    /**
     * Bulk Delete
     */
    protected function bulkDelete($assets)
    {
        // Prevent deleting assigned assets
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

    /**
     * Bulk Checkout Redirect
     * (Like Snipe-IT → redirect to checkout page)
     */
    protected function bulkCheckoutRedirect($assets)
    {
        session()->put('bulk_checkout_ids', $assets->pluck('id')->toArray());

        return redirect()->route('assets.bulk.checkout.form');
    }

    /**
     * Bulk Edit Redirect
     */
    protected function bulkEditRedirect($assets)
    {
        session()->put('bulk_edit_ids', $assets->pluck('id')->toArray());

        return redirect()->route('assets.bulk.edit.form');
    }





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





/**
 * Process Bulk Edit Update
 */
public function update(Request $request)
{
    $request->validate([
        'ids' => 'required|array|min:1',
        'status_id' => 'required|exists:status_labels,id'
    ]);

    $departmentId = DepartmentContext::id();

    $assets = Asset::where('department_id', $departmentId)
        ->whereIn('id', $request->ids)
        ->get();

    if ($assets->isEmpty()) {
        return redirect()->route('assets.index')
            ->with('error', 'No valid assets selected.');
    }

    DB::transaction(function () use ($assets, $request) {
        foreach ($assets as $asset) {

            // 🚫 Optional Protection:
            // Do not allow changing status of assigned assets
            if ($asset->assigned_to !== null) {
                continue;
            }

            $asset->update([
                'status_id' => $request->status_id
            ]);
        }
    });

    return redirect()->route('assets.index')
        ->with('success', 'Bulk status update successful.');
}


}
