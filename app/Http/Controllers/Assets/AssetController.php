<?php

namespace App\Http\Controllers\Assets;
use App\Models\Asset;
use App\Models\User;
use App\Services\AssetService;
use App\Http\Controllers\Controller;



use Illuminate\Http\Request;

class AssetController extends Controller
{

    public function index()  //Fetches all assets from the database
    {
        return view('assets.index', [
            'assets' => Asset::with('assigned')->get()  //Also loads the assigned user/entity using "with('assigned')
        ]);
    }

    
    public function show(Asset $asset)//Laravel automatically injects the asset using route model binding and showa all users
    {
        return view('assets.show', [
            'asset' => $asset,
            'users' => User::all()
        ]);
    }
    public function checkout(Request $request, Asset $asset) //Receives:asset from URL user ID from form submission
    {
        $user = User::findOrFail($request->user_id);//Finds the user safely

        AssetService::checkout($asset, $user); //calls logic from service folder

        return back()->with('success', 'Asset checked out');
    }

    public function checkin(Asset $asset) //removes the assignment with one clicks
    {
        AssetService::checkin($asset,);

        return back()->with('success', 'Asset checked in');
    }

  




}

