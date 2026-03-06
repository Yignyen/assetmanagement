@extends('layouts.admin')

@section('title','Create Manufacturer')
@section('page-title','Create Manufacturer')

@section('content')

<form method="POST" action="{{ route('manufacturers.store') }}">
@csrf

<div style="margin-bottom:15px">
<label>Name</label><br>
<input type="text" name="name" required>
</div>

<div style="margin-bottom:15px">
<label>Support URL</label><br>
<input type="text" name="support_url">
</div>

<div style="margin-bottom:15px">
<label>Warranty Lookup URL</label><br>
<input type="text" name="warranty_lookup_url">
</div>

<div style="margin-bottom:15px">
<label>Support Phone</label><br>
<input type="text" name="support_phone">
</div>

<div style="margin-bottom:15px">
<label>Support Email</label><br>
<input type="email" name="support_email">
</div>

<button class="btn btn-primary">
Create Manufacturer
</button>

</form>

@endsection

