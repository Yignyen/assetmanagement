@extends('layouts.admin')

@section('title', 'Assets')
@section('page-title', 'Assets')

@section('content')
<a href="{{ route('assets.create') }}" class="btn btn-primary">
    ➕ Add Asset
</a>
<br><br>
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
