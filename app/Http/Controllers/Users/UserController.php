<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\DepartmentContext;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * List users (department scoped)
     */
    public function index()
    {
        $departmentId = DepartmentContext::id();

        $users = User::where('department_id', $departmentId)
            ->with('department') // ✅ eager load
            ->latest()
            ->paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $departmentId = DepartmentContext::id();

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role'     => 'required|string',
        ]);

        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => bcrypt($request->password),
            'role'          => $request->role,
            'department_id' => $departmentId,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Show edit form
     */
    public function edit(User $user)
    {
        if ($user->department_id !== DepartmentContext::id()) {
            abort(403);
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        if ($user->department_id !== DepartmentContext::id()) {
            abort(403);
        }

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|string',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully');
    }

    /**
     * Delete user (soft delete)
     */
    public function destroy(User $user)
    {
        if ($user->department_id !== DepartmentContext::id()) {
            abort(403);
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully');
    }

    public function show(User $user)
{
    if ($user->department_id !== DepartmentContext::id()) {
        abort(403);
    }

    // ✅ EAGER LOAD assets + their models
    $user->load([
        'assets.model'
    ]);

    return view('users.show', compact('user'));
}

}
