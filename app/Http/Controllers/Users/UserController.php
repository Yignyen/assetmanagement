<?php
namespace App\Http\Controllers\Users;
use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;

class UserController extends Controller
{
    //for index    
    // READ (List users)
    public function index()
    {
        $users = User::latest()->get();
        return view('users.index', compact('users'));
    }

    // CREATE form
    public function create()
    {
        return view('users.create');
    }

    // STORE new user
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:6',
            'role'       => 'required',
            'department' => 'required',
        ]);

        User::create($request->all()); // password auto-hashed ✅

        return redirect()->route('users.index')
            ->with('success', 'User created successfully');
    }

    // EDIT form
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    // UPDATE user
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'role'       => 'required',
            'department' => 'required',
        ]);

        $user->update($request->except('password'));

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }

    // DELETE (Soft delete)
    public function destroy(User $user)
    {
        $user->delete(); // triggers asset unassign logic

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }
}
