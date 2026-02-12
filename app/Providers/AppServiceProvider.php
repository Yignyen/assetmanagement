<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Asset;
use Illuminate\Support\Facades\View;
use App\Support\DepartmentContext;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {   
         View::composer('layouts.admin', function ($view) {

        
        $departmentId = DepartmentContext::id();

        $baseQuery = Asset::where('department_id', $departmentId);

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

        $view->with('sidebarCounts', $counts);
    });
        
    }
}
