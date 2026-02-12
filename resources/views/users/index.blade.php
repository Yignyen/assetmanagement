@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'Users List')
@section('page-actions')
<a href="{{ route('users.create') }}" class="btn btn-primary">
    ➕ Add User
</a>
@endsection
@section('content')

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Department</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        @forelse($users as $user)
        <tr>
            <td>
            <a href="{{ route('users.show', $user) }}">
                {{ $user->name }}
            </a>
            </td>

            <td>{{ $user->email }}</td>
            <td>{{ ucfirst($user->role) }}</td>
            <td>{{ $user->department->name ?? '—' }}</td>

            <td>
                <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                    Edit
                </a>

                <form method="POST"
                      action="{{ route('users.destroy', $user) }}"
                      style="display:inline">
                    @csrf
                    @method('DELETE')

                    <button class="btn btn-danger"
                            onclick="return confirm('Delete user?')">
                        Delete
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5">No users found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

@endsection
