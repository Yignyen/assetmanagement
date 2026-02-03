@extends('layouts.admin')

@section('title', 'Edit Location')
@section('page-title', 'Edit Location')

@section('content')

<form method="POST" action="{{ route('locations.update', $location) }}">
    @csrf
    @method('PUT')

    <label>Name</label><br>
    <input type="text" name="name"
           value="{{ $location->name }}" required><br><br>

    <label>Parent (Department)</label><br>
    <select name="parent_id">
        <option value="">— Department —</option>
        @foreach($departments as $department)
            <option value="{{ $department->id }}"
                @selected($location->parent_id === $department->id)>
                {{ $department->name }}
            </option>
        @endforeach
    </select><br><br>
    <p>
    <strong>Type:</strong>
    {{ $location->parent_id ? 'Place' : 'Department' }}
</p>


    <label>Notes</label><br>
    <textarea name="notes">{{ $location->notes }}</textarea><br><br>

    <button class="btn btn-primary">Update</button>
    <a href="{{ route('locations.index') }}" class="btn btn-warning">Back</a>
</form>

@endsection
