@extends('layouts.admin')

@section('title', 'Bulk Checkout')
@section('page-title', 'Bulk Checkout')

@section('content')

<a href="{{ route('assets.index') }}" class="btn btn-warning">
    ← Back to Assets
</a>

<hr>

{{-- <h3>Bulk Assign ({{ count($assets) }} selected)</h3> --}}

@if(session('warning') && session('removed_assets'))
    <div class="alert alert-warning">

        <strong>The following assets were removed (not deployable or already assigned):</strong>

        <ul>
            @foreach(session('removed_assets') as $tag)
                <li>{{ $tag }}</li>
            @endforeach
        </ul>

        @if(session('remaining_assets') && count(session('remaining_assets')))
            <strong>Remaining assets ready for checkout:</strong>
            {{-- <ul>
                @foreach(session('remaining_assets') as $tag)
                    <li>{{ $tag }}</li>
                @endforeach
            </ul> --}}
        @else
            <strong>No valid assets remain selected. You may add new ones.</strong>
        @endif

    </div>
@endif
<hr>

{{-- =====================
   BULK CHECKOUT FORM
===================== --}}

<form method="POST" action="{{ route('assets.bulk.checkout.process') }}">
    @csrf
{{-- =====================
   ASSET SELECT (Snipe-IT Style)
===================== --}}

@include('components.asset-select', [
   'translated_name' => 'Assets',
   'fieldname' => 'selected_assets[]',
   'multiple' => true,
   'required' => true,
   'select_id' => 'assigned_assets_select',
   'asset_selector_div_id' => 'assets_to_checkout_div',
   'asset_ids' => old('selected_assets', $assets->pluck('id')->toArray()),
   'allAssets' => $allAssets
])

    <hr>

    <label>Assign To:</label><br>

    <select name="checkout_to_type" required onchange="toggleTarget(this.value)">
        <option value="">-- Select Type --</option>
        <option value="user">User</option>
        <option value="location">Room</option>
        <option value="asset">Another Asset</option>
    </select>

    <br><br>

    {{-- USER --}}
    <div id="user-select" style="display:none;">
        <label>User:</label><br>
        <select name="checkout_to_id" disabled>
            @foreach($users as $user)
                <option value="{{ $user->id }}">
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- LOCATION --}}
    <div id="location-select" style="display:none;">
        <label>Room:</label><br>
        <select name="checkout_to_id" disabled>
            @foreach($locations as $location)
                <option value="{{ $location->id }}">
                    {{ $location->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- ASSET --}}
    <div id="asset-select" style="display:none;">
        <label>Parent Asset:</label><br>
        <select name="checkout_to_id" disabled>
            @foreach($allAssets as $parent)
                <option value="{{ $parent->id }}">
                    {{ $parent->asset_tag }} - {{ $parent->name }}
                </option>
            @endforeach
        </select>
    </div>

    <br>

    <label>Note (optional):</label><br>
    <input type="text" name="note">

    <br><br>

    <button class="btn btn-primary">
        Assign Selected Assets
    </button>

</form>

{{-- =====================
   TOGGLE SCRIPT
===================== --}}
<script>
function toggleTarget(type) {

    document.querySelectorAll('[name="checkout_to_id"]').forEach(el => {
        el.disabled = true;
        el.closest('div').style.display = 'none';
    });

    if (type === 'user') {
        const el = document.querySelector('#user-select select');
        el.disabled = false;
        el.closest('div').style.display = 'block';
    }

    if (type === 'location') {
        const el = document.querySelector('#location-select select');
        el.disabled = false;
        el.closest('div').style.display = 'block';
    }

    if (type === 'asset') {
        const el = document.querySelector('#asset-select select');
        el.disabled = false;
        el.closest('div').style.display = 'block';
    }
}


/* 
$(document).ready(function() {

    $('#assigned_assets_select').select2({
        ajax: {
            url: '/ajax/assets',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return data;
            },
            cache: true
        },
        minimumInputLength: 1,
        placeholder: 'Search asset...',
        width: '100%'
    });

}); */

</script>

@section('scripts')
<script>
$('#assigned_assets_select').select2({
    ajax: {
        url: '/ajax/assets',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return { q: params.term };
        },
        processResults: function (data) {
            return data; // because we already returned { results: [...] }
        }
    },
    minimumInputLength: 1,
    placeholder: 'Search asset...',
    allowClear: true,
    width: '100%'
});
</script>
@endsection




@endsection