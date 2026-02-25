@extends('layouts.admin')

@section('title', 'Bulk Edit Assets')
@section('page-title', 'Bulk Edit Assets')

@section('content')

<h3>Bulk Edit ({{ count($assets) }} selected)</h3>

{{-- Alerts --}}
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul style="margin-bottom:0;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div style="background:#fff3cd;padding:15px;border-radius:8px;margin-bottom:20px;">
    You are about to edit <strong>{{ count($assets) }}</strong> assets.
    <br>
    Only fill the fields you want to change.
    <br><br>
    ⚠ If an asset is assigned, you cannot change it to a non-deployable status.
</div>

<form method="POST" action="{{ route('assets.bulk.edit.update') }}">
    @csrf

    {{-- Hidden IDs --}}
    @foreach($assets as $asset)
        <input type="hidden" name="selected_assets[]" value="{{ $asset->id }}">
    @endforeach


    {{-- ================= STATUS ================= --}}
    <div style="margin-bottom:25px;">
        <label><strong>Status</strong></label><br>

        <select name="status_id" class="form-control" style="max-width:350px;">
            <option value="">-- Do Not Change --</option>

            @foreach($statuses as $status)
                <option value="{{ $status->id }}">
                    {{ $status->name }}
                    @if(!$status->deployable)
                        (Undeployable)
                    @endif
                    @if($status->pending)
                        (Pending)
                    @endif
                </option>
            @endforeach
        </select>

        <small class="text-muted">
            Deployable → Can be checked out<br>
            Undeployable → Repair / Broken / Lost<br>
            Pending → Waiting for approval
        </small>
    </div>


    {{-- ================= LABEL ================= --}}
    <div style="margin-bottom:25px;">
        <label><strong>Label</strong></label><br>

        <input type="text"
               name="label"
               class="form-control"
               style="max-width:350px;"
               placeholder="Enter new label">

        <div style="margin-top:8px;">
            <label>
                <input type="checkbox" name="clear_label" value="1">
                Clear label for all selected
            </label>
        </div>
    </div>
    
        {{-- ================= MODEL CHANGE ================= --}}
<div style="margin-top:30px;">
    <label><strong>Model Change</strong></label><br>

    <select name="model_id" class="form-control" style="max-width:400px;">
        <option value="">-- Do Not Change --</option>

        @foreach ($models as $model)
            <option value="{{ $model->id }}">
                {{ $model->name }}
            </option>
        @endforeach
    </select>
</div>


    {{-- ================= ACTION BUTTONS ================= --}}
    <div style="margin-top:45px;">
        <button type="submit" class="btn btn-primary">
            Update Selected Assets
        </button>

        <a href="{{ route('assets.index') }}" class="btn btn-secondary">
            Cancel
        </a>
    </div>

</form>

@endsection
