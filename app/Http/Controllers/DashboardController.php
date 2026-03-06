<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use App\Models\Location;
use App\Models\Category;
use App\Support\DepartmentContext;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $departmentId = DepartmentContext::id();

        // selected category from dropdown
        $categoryId = $request->category;

        // base query
     $assetsQuery = Asset::where('department_id', $departmentId);

if ($categoryId) {
    $assetsQuery->whereHas('model.category', function ($q) use ($categoryId) {
        $q->where('id', $categoryId);
    });
}

        return view('dashboard.index', [

            // categories for dropdown
            'categories' => Category::all(),
            'selectedCategory' => $categoryId,

            // counts
            'assetsCount' => (clone $assetsQuery)->count(),

            'usersCount' => User::where('department_id',$departmentId)->count(),

            'locationsCount' => Location::where('department_id',$departmentId)->count(),

            'assignedCount' => (clone $assetsQuery)
                ->whereNotNull('assigned_to')
                ->count(),

            'notAvailableCount' => (clone $assetsQuery)
                ->whereHas('status', fn($q) =>
                $q->undeployable()->orWhere(fn($q) => $q->pending())->orwhere(fn($q)=> $q->archived())
                            )
                ->count(),

            'availableCount' => (clone $assetsQuery)
                ->whereNull('assigned_to')
                ->whereHas('status', fn($q) => $q->deployable() )
                ->count(),

            'assignedToUsers' => (clone $assetsQuery)
                ->where('assigned_type', User::class)
                ->count(),

            'assignedToLocations' => (clone $assetsQuery)
                ->where('assigned_type', Location::class)
                ->count(),

            'assignedToAssets' => (clone $assetsQuery)
                ->where('assigned_type', Asset::class)
                ->count(),
        ]);
    }
}