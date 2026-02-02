<?php

namespace App\Http\Controllers\Assets;
use App\Models\Asset;
use App\Models\User;
use App\Services\AssetService;
use App\Http\Controllers\Controller;



use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function checkout(Request $request, Asset $asset)
    {
        $user = User::findOrFail($request->user_id);

        AssetService::checkout($asset, $user);

        return back()->with('success', 'Asset checked out');
    }

    public function checkin(Asset $asset)
    {
        AssetService::checkin($asset);

        return back()->with('success', 'Asset checked in');
    }

  

public function show(Asset $asset)
{
    return view('assets.show', [
        'asset' => $asset,
        'users' => User::all()
    ]);
}

public function index()
{
    return view('assets.index', [
        'assets' => Asset::with('assigned')->get()
    ]);
}

}

