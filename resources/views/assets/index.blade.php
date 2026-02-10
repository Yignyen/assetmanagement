@extends('layouts.admin')

@section('title', 'Assets')
@section('page-title', 'Assets')

@section('content')
<a href="{{ route('assets.create') }}" class="btn btn-primary">
    ➕ Add Asset
</a>
<br><br>

@if(request('status') === 'available')
    <h3>Available Assets</h3>
@elseif(request('status') === 'assigned')
    <h3>Assigned Assets</h3>
@else
    <h3>All Assets</h3>
@endif

<form method="GET" action="{{ route('assets.index') }}" style="margin-bottom: 15px;">
    <input type="text"
           name="search"
           value="{{ request('search') }}"
           placeholder="Search assets like monitor or assigned to like admin user"
           style="padding:8px; width:340px;">

    {{-- keep status when searching --}}
    @if(request('status'))
        <input type="hidden" name="status" value="{{ request('status') }}">
    @endif

    <button class="btn btn-primary btn-sm">Search</button>

    @if(request('search'))
        <a href="{{ route('assets.index', request('status') ? ['status' => request('status')] : []) }}"
           class="btn btn-warning btn-sm">
            Clear
        </a>
    @endif
</form>


<br>

<div class="table-scroll">
    <table class="table table-bordered">
    <thead>
        <tr>
            <th>Lable</th>
            <th>Asset Tag</th>
            <th>Serial No</th>
            <th>Model</th>
            <th>Category</th>
            <th>Status</th>
            <th>Assigned To</th>
            <th>Check-in / Check-out</th> {{-- renamed --}}
            <th>Actions</th> {{-- NEW --}}
        </tr>
    </thead>

    <tbody>
        @forelse($assets as $asset)
            <tr>
                {{-- Asset label (can be NULL) --}}
                <td>{{ $asset->label  }}</td>

                <td>{{ $asset->asset_tag }}</td>

                <td>{{ $asset->serial_no ?? '—' }}</td>

                {{-- Model --}}
                <td>{{ $asset->model?->name ?? '—' }}</td>

                {{-- Category via model --}}
                <td>{{ $asset->model?->category?->name ?? '—' }}</td>

                {{-- Status --}}
                <td class="status-{{ $asset->status }}">
                    {{ ucfirst($asset->status) }}
                </td>

                {{-- Assigned --}}
                <td>
                    @if($asset->assigned)
                        {{ $asset->assigned->name ?? '—' }}
                    @else
                        —
                    @endif
                </td>
                <td>
                   <a href="{{ route('assets.show', $asset) }}"
                        class="btn btn-sm btn-outline-danger">
                        {{ $asset->assigned_to ? 'Check-in' : 'Check-out' }}
                    </a>


                </td>
                {{-- Actions --}}
    <td>
        <a href="{{ route('assets.edit', $asset) }}"
            class="btn btn-sm btn-primary">
                Edit
                </a>
    </td>


            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center">
                    No assets found
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
</div>

@endsection
