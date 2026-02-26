@extends('layouts.admin')

@section('title', 'Bulk Delete')
@section('page-title', 'Bulk Delete')

@section('content')

<a href="{{ route('assets.index') }}" class="btn btn-secondary">
    ← Cancel
</a>

<hr>

<div class="alert alert-warning">
    <strong>⚠ You are about to delete {{ $assets->count() }} asset(s).</strong><br>
    Once deleted, these assets can be restored,
    but they will no longer be associated with any users they are currently assigned to.
</div>

<div class="card">
    <div class="card-body p-0">

        <table class="table table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Asset Name</th>
                    <th>Location</th>
                    <th>Assigned To</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assets as $asset)
                    <tr>
                        <td>{{ $asset->id }}</td>
                        <td>{{ $asset->asset_tag }} - {{ $asset->name }}</td>
                        <td>{{ $asset->location?->name ?? '—' }}</td>
                        <td>
                            @if($asset->assigned_to)
                                <span class="text-danger">Assigned</span>
                            @else
                                <span class="text-success">Available</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>

<br>

<form method="POST" action="{{ route('assets.bulk.delete.process') }}">
    @csrf
    <button class="btn btn-danger float-end">
        ✓ Delete
    </button>
</form>

@endsection