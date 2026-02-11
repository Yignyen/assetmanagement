@extends('layouts.admin')

@section('title', 'Bulk Edit Assets')

@section('content')

<h3>Bulk Edit ({{ $assets->count() }} selected)</h3>

<form method="POST" action="{{ route('assets.bulk.edit.update') }}">
    @csrf

    {{-- Keep selected IDs --}}
    @foreach($assets as $asset)
        <input type="hidden" name="ids[]" value="{{ $asset->id }}">
    @endforeach

    <label>Change Status</label>
    <select name="status_id" required>
        <option value="">-- Select Status --</option>
        @foreach($statuses as $status)
            <option value="{{ $status->id }}">
                {{ $status->name }}
            </option>
        @endforeach
    </select>

    <br><br>

    <button class="btn btn-primary">
        Update Selected Assets
    </button>

</form>

@endsection
