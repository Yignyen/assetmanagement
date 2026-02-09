@extends('layouts.admin')

@section('title', 'User Details')
@section('page-title', 'User Details')

@section('content')

            <div class="card-body p-0">
            {{ $location->name }}
            @if($location->assets->isEmpty())
                <p class="text-muted p-3 mb-0">
                    No assets assigned to this location.
                </p>
            @else
                <table class="table table-striped table-bordered mb-0">
    <thead class="table-light">
        <tr>
            <th style="width: 50px;">#</th>
            <th>Asset</th>
            <th>Asset Tag</th>
            <th>Category</th>
            <th>Status</th>
            <th>Assigned Date</th>
        </tr>
    </thead>

    <tbody>
        @foreach($location->assets as $index => $asset)
            <tr>
                <td>{{ $index + 1 }}</td>

                {{-- Model + Serial --}}
                <td>
                    {{ $asset->model?->name ?? 'Unknown Model' }}
                    <br>
                    <small class="text-muted">
                        SN: {{ $asset->serial_no ?? '—' }}
                    </small>
                </td>

                {{-- Asset Tag --}}
                <td>
                    {{ $asset->asset_tag ?? '—' }}
                </td>

                {{-- Category --}}
                <td>
                    {{ $asset->model?->category?->name ?? '—' }}
                </td>

                {{-- Status --}}
                <td>
                    <span class="badge bg-primary">
                        {{ ucfirst($asset->status) }}
                    </span>
                </td>

                {{-- Assigned Date --}}
                <td>
                    {{ $asset->assigned_at
                        ? $asset->assigned_at->format('d M Y')
                        : '—'
                    }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

            @endif
        
    </div>



@endsection
