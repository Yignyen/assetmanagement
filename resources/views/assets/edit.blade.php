@extends('layouts.admin')

@section('title', 'Edit Asset')
@section('page-title', 'Edit Asset')

@section('content')

<a href="{{ route('assets.index') }}" class="btn btn-warning">
    ← Back to Assets
</a>

<hr>

{{-- =====================
   UPDATE ASSET FORM
===================== --}}
<h3>Edit Asset</h3>

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
               value="{{ old('name', $asset->name) }}">
    </p>

    <p>
        <label>Serial No</label><br>
        <input type="text"
               name="serial_no"
               value="{{ old('serial_no', $asset->serial_no) }}"
               required>
    </p>

    <p>
        <label>Model</label><br>
        <select name="model_id" required>
            @foreach($models as $model)
                <option value="{{ $model->id }}"
                    @selected($asset->model_id == $model->id)>
                    {{ $model->name }}
                </option>
            @endforeach
        </select>
    </p>
    <p>
    <label>Status</label><br>
    <select name="status_id" required>
        @foreach($statuses as $status)
            <option value="{{ $status->id }}"
                @selected($asset->status_id == $status->id)>
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
