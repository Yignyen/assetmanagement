@extends('layouts.admin')

@section('title', 'Locations')
@section('page-title', 'Locations')

@section('content')

<a href="{{ route('locations.create') }}" class="btn btn-primary">
    ➕ Add Location
</a>

<br><br>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Department</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        @forelse($locations as $location)
        <tr>
            <td>
            <a href="{{ route('locations.show', $location) }}">
                {{ $location->name }}
            </a>
            </td>
            <td>{{ $location->department->name ?? '—' }}</td>
            <td>
                <a href="{{ route('locations.edit', $location) }}" class="btn btn-warning">
                    Edit
                </a>

                <form action="{{ route('locations.destroy', $location) }}"
                      method="POST"
                      style="display:inline;">
                    @csrf
                    @method('DELETE')

                    <button class="btn btn-danger"
                            onclick="return confirm('Delete this location?')">
                        Delete
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="3" style="text-align:center;">
                No locations found.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

@endsection
