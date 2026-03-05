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
         {{-- categories  (REQUIRED) --}}
<div style="margin-bottom:15px">

<label><strong>Category</strong></label>

<div style="display:flex; gap:8px; margin-top:5px">

<select id="category-select" name="category_id" style="width:300px">
<option value="">Select Category</option>

@foreach ($categories as $category)
<option value="{{ $category->id }}">
{{ $category->name }}
</option>
@endforeach

</select>

<button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#newCategoryModal">
+ New
</button>

</div>
</div>

<br>
    {{-- Model (REQUIRED) --}}
<div style="margin-bottom:15px">

<label><strong>Model</strong></label>

<div style="display:flex; gap:8px; margin-top:5px">

<select id="model-select" name="model_id" style="width:300px">
<option value="">Select Model</option>
</select>

<button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#newModelModal">
+ New
</button>

</div>
</div>

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
  {{-- Service Tag --}}

    <div id="service-tag-wrapper" style="display:none;">
<p>
<label><strong>Dell Service Tag</strong></label><br>
<input type="text" name="service_tag">
</p>
</div>

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
@section('scripts')
<script>
let modelData = {};

$('#category-select').select2({
    placeholder: "Select Category",
    width: '300px'
});

$('#model-select').select2({
    placeholder: "Select Model",
    width: '300px'
});

$('#category-select').on('change', function () {

    let categoryId = $(this).val();

    $('#model-select').empty().append('<option value="">Loading...</option>').trigger('change');

    if (!categoryId) {
        $('#model-select').html('<option value="">Select Model</option>');
        return;
    }

    $.get('/ajax/models-by-category', { category_id: categoryId }, function (models) {

        let options = '<option value="">Select Model</option>';

        modelData = {};

        models.forEach(function (model) {

            modelData[model.id] = model;

            options += `<option value="${model.id}">${model.name}</option>`;
        });

        $('#model-select').html(options).trigger('change');

    });

});

$('#model-select').on('change', function () {

    let modelId = $(this).val();

    if (!modelId) {
        $('#service-tag-wrapper').hide();
        return;
    }

    let manufacturerId = modelData[modelId].manufacturer_id;

    if (manufacturerId == 1) {   // Dell ID
        $('#service-tag-wrapper').show();
    } else {
        $('#service-tag-wrapper').hide();
    }

});
</script>
@endsection

@endsection
