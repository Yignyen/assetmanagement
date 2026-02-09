@extends('layouts.admin')

@section('title', 'Create Asset')
@section('page-title', 'Create Asset')

@section('content')

<a href="{{ route('assets.index') }}" class="btn btn-warning">
    ← Back to Assets
</a>

<hr>

@if ($errors->any())
    <div style="color:red; margin-bottom:10px;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('assets.store') }}">
    @csrf

    {{-- Model (REQUIRED) --}}
    <p>
        <label><strong>Model</strong></label><br>
        <select name="model_id" required>
            <option value="">— Select Model —</option>
            @foreach ($models as $model)
                <option value="{{ $model->id }}" @selected(old('model_id') == $model->id)>
                    {{ $model->category->name }} — {{ $model->name }}
                </option>
            @endforeach
        </select>
    </p>

    {{-- Asset Label --}}
<p>
    <label><strong>Asset Label (Optional)</strong></label><br>
    <input
        type="text"
        name="label"
        value="{{ old('label') }}"
        placeholder="CEO Laptop"
    >
</p>


    {{-- Asset Tag --}}
    <p>
        <label><strong>Asset Tag</strong></label><br>
        <input
            type="text"
            name="asset_tag"
            value="{{ old('asset_tag', \App\Services\AssetTagService::generate()) }}"
            placeholder="Auto-generated (editable)"
            required
        >
        <small class="text-muted">
                 Asset tag is auto-generated, but you may edit it.
        </small>

    </p>

    {{-- Serial Number --}}
    <p>
        <label><strong>Serial No</strong></label><br>
        <input
            type="text"
            name="serial_no"
            value="{{ old('serial_no') }}"
        >
    </p>

    {{-- Purchase Date --}}
    <p>
        <label><strong>Purchase Date</strong></label><br>
        <input
            type="date"
            name="purchase_date"
            value="{{ old('purchase_date') }}"
        >
    </p>

    <hr>

    <button type="submit" class="btn btn-primary">
        Create Asset
    </button>
</form>

@endsection
