<?php
namespace App\Http\Controllers\Users;
use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;

class UserController extends Controller
{
    //for index

    public function index()
    {
        return view('users.index', [
            'users' => User::all()
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();   // 👈 SOFT DELETE happens here

        return back()->with('success', 'User deleted successfully');
    }
}
