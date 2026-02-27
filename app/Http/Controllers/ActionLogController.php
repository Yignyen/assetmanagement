<?php

namespace App\Http\Controllers;

use App\Models\ActionLog;
use App\Support\DepartmentContext;


class ActionLogController extends Controller
{
    public function index()
    {
        $departmentId = DepartmentContext::id();

        $logs = ActionLog::with(['actor', 'item', 'target'])
            ->where('department_id', $departmentId)
            ->latest('action_date')
            ->get();

        return view('assets.asset_actionlog', compact('logs'));
    }
}
