@extends('layouts.admin')

@section('title', 'Bulk Checkout')
@section('page-title', 'Bulk Checkout')

@section('content')

<a href="{{ route('assets.index') }}" class="btn btn-warning">
    ← Back to Assets
</a>

<hr>

<h3>Bulk Assign ({{ count($assets) }} selected)</h3>

{{-- Alerts --}}
@if(session('success'))
    <p style="color: green">{{ session('success') }}</p>
@endif

@if(session('error'))
    <p style="color: red">{{ session('error') }}</p>
@endif

@if ($errors->any())
    <div style="color:red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<hr>

{{-- =====================
   BULK CHECKOUT FORM
===================== --}}

<form method="POST" action="{{ route('assets.bulk.checkout.process') }}">
    @csrf

    {{-- Hidden Asset IDs --}}
    @foreach($assets as $asset)
        <input type="hidden" name="ids[]" value="{{ $asset->id }}">
    @endforeach

    <h4>Selected Assets:</h4>
    <ul>
        @foreach($assets as $asset)
            <li>
                {{ $asset->asset_tag }} - {{ $asset->name }}
            </li>
        @endforeach
    </ul>

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
</script>

@endsection