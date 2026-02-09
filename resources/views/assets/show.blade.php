@extends('layouts.admin')

@section('title', 'Asset Details')
@section('page-title', 'Asset Details')

@section('content')

<a href="{{ route('assets.index') }}" class="btn btn-warning">
    ← Back to Assets
</a>

<hr>

<p><strong>Asset Tag:</strong> {{ $asset->asset_tag }}</p>
<p><strong>Name:</strong> {{ $asset->name }}</p>

<p>
    <strong>Status:</strong>
    <span class="status-{{ $asset->assigned_to ? 'assigned' : 'available' }}">
        {{ $asset->assigned_to ? 'Assigned' : 'Available' }}
    </span>
</p>

@if(session('success'))
    <p style="color: green">{{ session('success') }}</p>
@endif

@if(session('error'))
    <p style="color: red">{{ session('error') }}</p>
@endif

<hr>

{{-- =====================
   CHECKOUT FORM
===================== --}}
@if($asset->assigned_to === null)
    <h3>Assign Asset</h3>

    <form method="POST" action="{{ route('assets.checkout', $asset) }}">
        @csrf

        <label>Assign To:</label><br>

        <select name="checkout_to_type" required onchange="toggleTarget(this.value)">
            <option value="">-- Select Type --</option>
            <option value="user">User</option>
            <option value="location">Room</option>
            <option value="asset">Another Asset</option>
        </select>

        <br><br>

        <div id="user-select" style="display:none;">
            <label>User:</label><br>
            <select name="checkout_to_id" disabled>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div id="asset-select" style="display: none">
    <label>Parent Asset:</label><br>

    <select name="checkout_to_id">
        <option value="">-- Select Parent Asset --</option>

        @foreach($assets as $parent)
            <option value="{{ $parent->id }}">
                {{ $parent->model->name }} - {{ $parent->serial_no }} - {{ $parent->asset_tag }}
            </option>
        @endforeach
    </select>
</div>


        <div id="location-select" style="display:none;">
            <label>Room:</label><br>
            <select name="checkout_to_id" disabled>
                @foreach($locations as $location)
                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
            </select>
        </div>

        <br>

        <label>Note (optional):</label><br>
        <input type="text" name="note">

        <br><br>

        <button class="btn btn-primary">Assign</button>
    </form>
@endif

{{-- =====================
   CHECKIN FORM
===================== --}}
@if($asset->assigned_to !== null)
    <h3>Unassign Asset</h3>

    <p>
        <strong>Assigned To:</strong>
        {{ class_basename($asset->assigned_type) }}
        @if(isset($asset->assigned->name))
            ({{ $asset->assigned->name }})
        @endif
    </p>

    <form method="POST" action="{{ route('assets.checkin', $asset) }}">
        @csrf

        {{-- 👇 this decides where to go AFTER check-in --}}
        <input type="hidden"
            name="redirect_to"
            value="{{ session('redirect_after_checkin') }}">

            
        <label>Note (optional):</label><br>
        <input type="text" name="note">

        <br><br>

        <button class="btn btn-danger">Unassign</button>
    </form>
@endif

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
