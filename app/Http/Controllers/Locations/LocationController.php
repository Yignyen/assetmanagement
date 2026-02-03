<?php

namespace App\Http\Controllers\Locations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    //

    public function index()
    {
        $departments = Location::whereNull('parent_id')
            ->with('children')
            ->get();

        return view('locations.index', compact('departments'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        // Only departments can be parents
        $departments = Location::whereNull('parent_id')->get();

        return view('locations.create', compact('departments'));
    }

    /**
     * Store location
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:191',
            'parent_id' => 'nullable|exists:locations,id',
            'notes'     => 'nullable|string',
        ]);

        Location::create($request->only('name', 'parent_id', 'notes'));

        return redirect()
            ->route('locations.index')
            ->with('success', 'Location created successfully');
    }

    /**
     * Show edit form
     */
    public function edit(Location $location)
    {
        $departments = Location::whereNull('parent_id')
            ->where('id', '!=', $location->id)
            ->get();

        return view('locations.edit', compact('location', 'departments'));
    }

    /**
     * Update location
     */
    public function update(Request $request, Location $location)
    {
        $request->validate([
            'name'      => 'required|string|max:191',
            'parent_id' => 'nullable|exists:locations,id',
            'notes'     => 'nullable|string',
        ]);

        $location->update($request->only('name', 'parent_id', 'notes'));

        return redirect()
            ->route('locations.index')
            ->with('success', 'Location updated successfully');
    }

    /**
     * Delete location
     */
    public function destroy(Location $location)
    {
        // Prevent deleting department with places
        if ($location->children()->exists()) {
            return back()->with('error', 'Cannot delete department with places');
        }

        // Prevent deleting location with assets
        if ($location->assets()->exists()) {
            return back()->with('error', 'Cannot delete location with assets');
        }

        $location->delete();

        return redirect()
            ->route('locations.index')
            ->with('success', 'Location deleted successfully');
    }
}
