<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use App\Models\Location;
use App\Support\DepartmentContext;

class DashboardController extends Controller
{
    public function index()

    {
        $departmentId = DepartmentContext::id();
        return view('dashboard.index', [
            // counts
            'assetsCount'    => Asset::where('department_id',$departmentId)->count(),
            'usersCount'     => User::where('department_id',$departmentId)->count(),
            'locationsCount' => Location::where('department_id',$departmentId)->count(),

            // chart data
            'assignedCount' => Asset::where('department_id', $departmentId)
                        ->whereNotNull('assigned_to')
                        ->count(),

            'availableCount' => Asset::where('department_id', $departmentId)
                        ->whereNull('assigned_to')
                        ->whereHas('status', fn($q) => $q->deployable())
                        ->count(),

            
            'assignedToUsers' => Asset::where('department_id', $departmentId)->where('assigned_type', User::class)->count(),

            'assignedToLocations' => Asset::where('department_id', $departmentId)->where('assigned_type', Location::class)->count(),

            'assignedToAssets' => Asset::where('department_id', $departmentId)->where('assigned_type', Asset::class)->count(),
        ]);
    }
}
