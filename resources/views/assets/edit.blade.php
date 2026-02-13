@extends('layouts.admin')

@section('title', 'Edit Asset')
@section('page-title', 'Edit Asset')

@section('content')

<a href="{{ route('assets.index') }}" class="btn btn-warning">
    ← Back to Assets
</a>

<hr>

<h3>Edit Asset</h3>

{{-- =====================
   FLASH MESSAGES
===================== --}}
@if(session('warning'))
    <div style="background:#fff3cd; padding:10px; margin-bottom:15px; color:#856404; border-radius:5px;">
        {{ session('warning') }}
    </div>
@endif

@if(session('success'))
    <div style="background:#d4edda; padding:10px; margin-bottom:15px; color:#155724; border-radius:5px;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background:#f8d7da; padding:10px; margin-bottom:15px; color:#721c24; border-radius:5px;">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div style="background:#f8d7da; padding:10px; margin-bottom:15px; color:#721c24; border-radius:5px;">
        <ul style="margin:0; padding-left:20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- =====================
   UPDATE ASSET FORM
===================== --}}
<form method="POST" action="{{ route('assets.update', $asset) }}">
    @csrf
    @method('PUT')

    <p>
        <strong>Asset Tag:</strong>
        {{ $asset->asset_tag }}
    </p>

    <p>
        <label>Asset Name</label><br>
        <input type="text"
               name="name"
               value="{{ old('name', $asset->name) }}"
               style="width:300px;">
    </p>

    <p>
        <label>Serial No</label><br>
        <input type="text"
               name="serial_no"
               value="{{ old('serial_no', $asset->serial_no) }}"
               required
               style="width:300px;">
    </p>

    <p>
        <label>Model</label><br>
        <select name="model_id" required style="width:310px;">
            @foreach($models as $model)
                <option value="{{ $model->id }}"
                    @selected(old('model_id', $asset->model_id) == $model->id)>
                    {{ $model->name }}
                </option>
            @endforeach
        </select>
    </p>

    <p>
        <label>Status</label><br>
        <select name="status_id" required style="width:310px;">
            @foreach($statuses as $status)
                <option value="{{ $status->id }}"
                    @selected(old('status_id', $asset->status_id) == $status->id)>
                    {{ $status->name }}
                </option>
            @endforeach
        </select>
    </p>

    <button class="btn btn-success">
        Update Asset
    </button>
</form>

<hr>

{{-- =====================
   DELETE ASSET (DANGER)
===================== --}}
<h4 class="text-danger">Danger Zone</h4>
<p class="text-muted">
    Deleting this asset is permanent and cannot be undone.
</p>

<form method="POST"
      action="{{ route('assets.destroy', $asset) }}"
      onsubmit="return confirm('Are you sure you want to permanently delete this asset?');">
    @csrf
    @method('DELETE')

    <button class="btn btn-danger">
        Delete Asset
    </button>
</form>

@endsection
