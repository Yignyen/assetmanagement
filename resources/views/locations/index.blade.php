@extends('layouts.admin')

@section('title', 'Locations')
@section('page-title', 'Locations')

@section('content')

<a href="{{ route('locations.create') }}" class="btn btn-primary">
    ➕ Add Location
</a>

<br><br>

@foreach($departments as $department)
    <div style="margin-bottom:20px;">
        <strong>{{ $department->name }}</strong>

        <div style="margin-left:20px; margin-top:8px;">
            @forelse($department->children as $place)
                <div style="margin-bottom:6px;">
                    • {{ $place->name }}

                    <a href="{{ route('locations.edit', $place) }}"
                       class="btn btn-warning">Edit</a>

                    <form action="{{ route('locations.destroy', $place) }}"
                          method="POST"
                          style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger"
                                onclick="return confirm('Delete this location?')">
                            Delete
                        </button>
                    </form>
                </div>
            @empty
                <em style="color:#6b7280;">No places</em>
            @endforelse
        </div>
    </div>
@endforeach

@endsection
