<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use App\Models\Location;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index', [
            // counts
            'assetsCount'    => Asset::count(),
            'usersCount'     => User::count(),
            'locationsCount' => Location::count(),

            // chart data
            'assignedCount'  => Asset::where('status', 'assigned')->count(),
            'availableCount' => Asset::where('status', 'available')->count(),

            'assignedToUsers'     => Asset::where('assigned_type', User::class)->count(),
            'assignedToLocations' => Asset::where('assigned_type', Location::class)->count(),
        ]);
    }
}
