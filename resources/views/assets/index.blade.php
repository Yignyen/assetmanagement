@extends('layouts.admin')

@section('title', 'Assets')
@section('page-title', 'Assets')

@section('content')

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Asset Name</th>
            <th>Serial No</th>
            <th>Asset Tag</th>
            <th>Category</th>
            <th>Status</th>
            <th>Assigned To</th>
            <th>Purchase Date</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @forelse($assets as $asset)
            <tr>
                <td>{{ $asset->id }}</td>
                <td>{{ $asset->name }}</td>
                <td>{{ $asset->serial_no }}</td>
                <td>{{ $asset->asset_tag }}</td>
                <td>{{ $asset->category->name ?? '—' }}</td>

                <td class="{{ $asset->assigned ? 'status-assigned' : 'status-available' }}">
                         {{ $asset->assigned ? 'Assigned' : 'Available' }}
                </td>


                <td>
                    @if($asset->assigned)
                        {{ $asset->assigned->name }}
                    @else
                        —
                    @endif
                </td>

                <td>{{ $asset->purchase_date ?? '—' }}</td>

                <td>
                    <a href="{{ route('assets.show', $asset) }}">
                        View / Update
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="text-align:center;">
                    No assets found
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection
