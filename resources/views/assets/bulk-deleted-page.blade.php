@extends('layouts.admin')

@section('title', 'Bulk Deleted Page')
@section('page-title', 'Bulk Deleted Page')

@section('content')

<br><br>
{{-- =============================================
   BULK ACTION FORM (POST)
   This wraps the bulk dropdown + table
============================================== --}}
{{-- <div style="display: flex; justify-content:flex-start; margin-bottom:15px"> --}}
{{-- <form method="POST" action="{{ route('assets.bulk.handle') }}" id="bulk-form">
    @csrf

    {{-- ================================
       BULK ACTION BAR (Snipe-IT Style)
    ================================= --}}
    

        {{-- Dropdown for selecting bulk action 
        <div style="display:flex; justify-content:flex-start; gap:8px; margin-bottom:15px;">
        <select name="bulk_action" style="padding:8px; width:240px;">
           
            <option value="edit">Bulk Edit</option>
            <option value="checkout">Bulk Checkout</option>
            <option value="delete">Bulk Delete</option>
            <option value="restore">Bulk restore</option>
            
        </select>

        {{-- Submit bulk action 
        <button type="submit" class="btn btn-primary btn-sm">
            Go
        </button>
   
</div>
</form> --}}

{{-- ================================
   SEARCH FORM (GET)
   Keeps type filter when searching
================================ --}}
<div style="display: flex; justify-content:flex-end; margin-bottom:15px">
<form method="GET" action="{{ route('assets.index') }}">
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
</div>



<div class="table-scroll">
    <table class="table table-bordered">

        <thead>
            <tr>    {{-- select all checkbox --}}
                    <th>
                        <input type="checkbox" id="select-all">
                    </th>


                    <th>Label</th>
                    <th>Asset Tag</th>
                    <th>Serial No</th>
                    <th>Model</th>
                    <th>Category</th>
                    <th>deleted at</th>
                    <th>Status</th>
                
                    <th class="sticky-action">Actions</th>
            </tr>
        </thead>

            <tbody>
                @forelse ($assets_deleted as $asset )

                <tr>

                        <td>
                            <input type="checkbox"
                                   name="ids[]"
                                   value="{{ $asset->id }}"
                                   class="row-checkbox">
                        </td>

                    <td> {{ $asset->label }}  </td>
                    <td> {{ $asset->asset_tag }}  </td>
                    <td> {{ $asset->serial_no ?? '---' }}  </td>
                    <td> {{ $asset->model?->name ?? '---' }}  </td>
                    <td> {{ $asset->model?->category?->name ?? '---' }}  </td>
                    <td> {{ $asset->deleted_at }} </td>
                   
                    <td> {{ $asset->status?->name ?? '---' }}  </td>
                    

                    <td>
                        <form method="POST"
      action="{{ route('assets.restore', $asset->id) }}"
      style="display:inline;"
      onsubmit="return confirm('⚠️ Are you sure you want to restore this asset?');">
    @csrf
    <button type="submit" class="btn btn-sm btn-outline-danger">
        Restore
    </button>
</form>
                    </td>
                    
                </tr>
                    
                @empty

                <tr>
                    <td colspan="5">No deleted records found</td>
                </tr>
                    
                @endforelse
            </tbody>
    </table>
</div>




{{-- =============================================
   JAVASCRIPT
   Handles select all + validation</form>
============================================== --}}
<script>

// Select All checkbox behavior
document.getElementById('select-all').addEventListener('change', function() {
    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.checked = this.checked;
    });
});

// Prevent submitting bulk form without action or selection
document.getElementById('bulk-form').addEventListener('submit', function(e) {

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