@extends('layouts.admin')

@section('title','Create Model')
@section('page-title','Create Model')

@section('content')

<a href="{{ route('assets.create') }}" class="btn btn-warning">
← Back to Create Asset
</a>

<hr>

<form method="POST" action="{{ route('models.store') }}">
@csrf



{{-- CATEGORY --}}
<div style="margin-bottom:15px">
<label><strong>Category</strong></label><br>

<select name="category_id" style="width:300px" required>

<option value="">Select Category</option>

@foreach($categories as $category)

<option value="{{ $category->id }}">
{{ $category->name }}
</option>

@endforeach

</select>

</div>


{{-- MANUFACTURER --}}
<div style="margin-bottom:15px">

<label><strong>Manufacturer</strong></label>

<div style="display:flex; gap:8px; margin-top:5px">

<select name="manufacturer_id" style="width:300px">

<option value="">Select Manufacturer</option>

@foreach($manufacturers as $manufacturer)

<option value="{{ $manufacturer->id }}">
{{ $manufacturer->name }}
</option>

@endforeach

</select>

<a href="{{ route('manufacturers.create') }}"
   class="btn btn-sm btn-secondary">

+ New Manufacturer

</a>

</div>

</div>


{{-- MODEL NAME --}}
<div style="margin-bottom:15px">
<label><strong>Model Name</strong></label><br>

<input type="text"
       name="name"
       required
       style="width:300px">
</div>








{{-- REQUIRE SERIAL --}}
<div style="margin-bottom:15px">

<label>

<input type="checkbox" name="require_serial">

Require Serial Number

</label>

</div>


<button class="btn btn-primary">
Create Model
</button>

</form>

@endsection