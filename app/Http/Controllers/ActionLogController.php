<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActionLog;

class ActionLogController extends Controller
{
    public function index()
    {
        $logs = ActionLog::with(['actor', 'item', 'target'])
            ->latest('action_date')
            ->get();

        return view('action_logs.index', compact('logs'));
    }
}
