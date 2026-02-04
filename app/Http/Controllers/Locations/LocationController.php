<?php

namespace App\Http\Controllers\Locations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Support\DepartmentContext;

class LocationController extends Controller
{
    /**
     * List all locations for current department
     */
    public function index()
    {
        $locations = Location::where('department_id', DepartmentContext::id())
            ->get();

        return view('locations.index', compact('locations'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('locations.create');
    }

    /**
     * Store new location
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:191',
            'notes' => 'nullable|string',
        ]);

        Location::create([
            'name'          => $request->name,
            'notes'         => $request->notes,
            'department_id' => DepartmentContext::id(),
        ]);

        return redirect()
            ->route('locations.index')
            ->with('success', 'Location created successfully');
    }

    /**
     * Show edit form
     */
    public function edit(Location $location)
    {
        if ($location->department_id !== DepartmentContext::id()) {
            abort(403);
        }

        return view('locations.edit', compact('location'));
    }

    /**
     * Update location
     */
    public function update(Request $request, Location $location)
    {
        if ($location->department_id !== DepartmentContext::id()) {
            abort(403);
        }

        $request->validate([
            'name'  => 'required|string|max:191',
            'notes' => 'nullable|string',
        ]);

        $location->update([
            'name'  => $request->name,
            'notes' => $request->notes,
        ]);

        return redirect()
            ->route('locations.index')
            ->with('success', 'Location updated successfully');
    }

    /**
     * Delete location
     */
    public function destroy(Location $location)
    {
        if ($location->department_id !== DepartmentContext::id()) {
            abort(403);
        }

        // Prevent deleting location with assets
        if ($location->assets()->exists()) {
            return back()->with('error', 'Cannot delete location with assigned assets');
        }

        $location->delete();

        return redirect()
            ->route('locations.index')
            ->with('success', 'Location deleted successfully');
    }
}
