@extends('layouts.admin')

@section('title', 'Assets')
@section('page-title', 'Assets')

@section('content')

{{-- ================================
   ADD NEW ASSET BUTTON
================================ --}}
<a href="{{ route('assets.create') }}" class="btn btn-primary">
    ➕ Add Asset
</a>

<br><br>

{{-- ================================
   PAGE HEADING BASED ON FILTER
================================ --}}
@if(request('type') === 'rtd')
    <h3>Ready To Deploy Assets</h3>
@elseif(request('type') === 'deployed')
    <h3>Deployed Assets</h3>
@elseif(request('type') === 'pending')
    <h3>Pending Assets</h3>
@elseif(request('type') === 'undeployable')
    <h3>Undeployable Assets</h3>
@elseif(request('type') === 'archived')
    <h3>Archived Assets</h3>
@else
    <h3>All Assets</h3>
@endif


{{-- ================================
   SEARCH FORM (GET)
   Keeps type filter when searching
================================ --}}
<form method="GET" action="{{ route('assets.index') }}" style="margin-bottom: 15px;">
    <input type="text"
           name="search"
           value="{{ request('search') }}"
           placeholder="Search assets"
           style="padding:8px; width:340px;">

    {{-- Preserve lifecycle filter --}}
    @if(request('type'))
        <input type="hidden" name="type" value="{{ request('type') }}">
    @endif

    <button class="btn btn-primary btn-sm">Search</button>

    @if(request('search'))
        <a href="{{ route('assets.index', request('type') ? ['type' => request('type')] : []) }}"
           class="btn btn-warning btn-sm">
            Clear
        </a>
    @endif
</form>



{{-- =============================================
   BULK ACTION FORM (POST)
   This wraps the bulk dropdown + table
============================================== --}}
<form method="POST" action="{{ route('assets.bulk.handle') }}">
    @csrf

    {{-- ================================
       BULK ACTION BAR (Snipe-IT Style)
    ================================= --}}
    <div style="display:flex;gap:10px;align-items:center;margin-bottom:15px">

        {{-- Dropdown for selecting bulk action --}}
        <select name="bulk_action" class="bulk-select">
            <option value="">Bulk Actions</option>
            <option value="edit">Bulk Edit</option>
            <option value="checkout">Bulk Checkout</option>
            <option value="delete">Bulk Delete</option>
            <option value="maintenance">Add Maintenance</option>
            <option value="labels">Generate Labels</option>
        </select>

        {{-- Submit bulk action --}}
        <button type="submit" class="btn btn-primary btn-sm">
            Go
        </button>
    </div>


    {{-- ================================
       TABLE WRAPPER
    ================================= --}}
    <div class="table-scroll">
        <table class="table table-bordered">

            {{-- ================================
               TABLE HEADER
            ================================= --}}
            <thead>
                <tr>

                    {{-- Select All Checkbox --}}
                    <th>
                        <input type="checkbox" id="select-all">
                    </th>

                    <th>Label</th>
                    <th>Asset Tag</th>
                    <th>Serial No</th>
                    <th>Model</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th class="sticky-check">Check-in / Check-out</th>
                    <th class="sticky-action">Actions</th>

                </tr>
            </thead>


            {{-- ================================
               TABLE BODY
            ================================= --}}
            <tbody>
                @forelse($assets as $asset)
                    <tr>

                        {{-- Row Checkbox --}}
                        <td>
                            <input type="checkbox"
                                   name="ids[]"
                                   value="{{ $asset->id }}"
                                   class="row-checkbox">
                        </td>

                        <td>{{ $asset->label }}</td>
                        <td>{{ $asset->asset_tag }}</td>
                        <td>{{ $asset->serial_no ?? '—' }}</td>
                        <td>{{ $asset->model?->name ?? '—' }}</td>
                        <td>{{ $asset->model?->category?->name ?? '—' }}</td>

                        {{-- ================================
                           STATUS DISPLAY LOGIC
                        ================================= --}}
                        <td>
                            @if($asset->status)

                                @php
                                    $status = $asset->status;
                                    $isAssigned = $asset->assigned_to !== null;
                                @endphp

                                @if($status->archived)
                                    <span class="status-dot dot-gray"></span>
                                    {{ $status->name }}

                                @elseif($status->pending)
                                    <span class="status-dot dot-orange"></span>
                                    {{ $status->name }}

                                @elseif(!$status->deployable)
                                    <span class="status-dot dot-red"></span>
                                    {{ $status->name }}

                                @else
                                    @if($isAssigned)
                                        <span class="status-dot dot-blue"></span>
                                        <span class="text-blue">
                                            {{ $status->name }}
                                        </span>
                                        <span class="status-tag">Deployed</span>
                                    @else
                                        <span class="status-dot dot-green"></span>
                                        <span class="text-green">
                                            {{ $status->name }}
                                        </span>
                                    @endif
                                @endif

                            @endif
                        </td>

                        {{-- Assigned --}}
                        <td>
                            {{ $asset->assigned?->name ?? '—' }}
                        </td>

                        {{-- ================================
                           CHECK-IN / CHECK-OUT BUTTON
                        ================================= --}}
                        <td class="sticky-check">

                            @php
                                $isDeployable = $asset->status?->isDeployable();
                                $isAssigned = $asset->assigned_to !== null;
                            @endphp

                            @if(!$isAssigned && !$isDeployable)

                                {{-- Disabled if undeployable --}}
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        disabled
                                        title="This asset has a status label that is undeployable and cannot be checked out at this time.">
                                    Check-out
                                </button>

                            @else

                                <a href="{{ route('assets.show', $asset) }}"
                                   class="btn btn-sm btn-outline-danger">
                                    {{ $isAssigned ? 'Check-in' : 'Check-out' }}
                                </a>

                            @endif

                        </td>

                        {{-- Actions --}}
                        <td class="sticky-action">
                            <a href="{{ route('assets.edit', $asset) }}"
                               class="btn btn-sm btn-primary">
                                Edit
                            </a>
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="10" class="text-center">
                            No assets found
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

</form>


{{-- =============================================
   JAVASCRIPT
   Handles select all + validation
============================================== --}}
<script>

// Select All checkbox behavior
document.getElementById('select-all').addEventListener('change', function() {
    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.checked = this.checked;
    });
});

// Prevent submitting bulk form without action or selection
document.querySelector('form').addEventListener('submit', function(e) {

    const action = document.querySelector('select[name="bulk_action"]').value;
    const selected = document.querySelectorAll('.row-checkbox:checked');

    if (!action) {
        alert('Please select a bulk action.');
        e.preventDefault();
        return;
    }

    if (selected.length === 0) {
        alert('Please select at least one asset.');
        e.preventDefault();
    }
});

</script>

@endsection
