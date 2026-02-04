@extends('layouts.admin')

@section('title', 'Create Location')
@section('page-title', 'Create Location')

@section('content')

<form method="POST" action="{{ route('locations.store') }}">
    @csrf

    <label>Name</label><br>
    <input type="text" name="name" required><br><br>

    <label>Notes</label><br>
    <textarea name="notes"></textarea><br><br>

    <button class="btn btn-primary">Create</button>
    <a href="{{ route('locations.index') }}" class="btn btn-warning">Back</a>
</form>

@endsection
