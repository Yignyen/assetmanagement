@extends('layouts.admin')

@section('title','Create Category')
@section('page-title','Create Category')

@section('content')

<a href="{{ url()->previous() }}" class="btn btn-secondary mb-3">
← Back
</a>

<form method="POST" action="{{ route('categories.store') }}">

@csrf

<div class="mb-3">

<label>Category Name</label><br>

<input type="text"
       name="name"
       class="form-control"
       required>

</div>

<br>


<div class="mb-3">

<label>Type</label>
<br>

<select name="type" class="form-control">

<option value="asset">Asset</option>
<option value="accessory">Accessory</option>
<option value="component">Component</option>

</select>

</div>

<br>


<div class="mb-3">

<label>Description</label>
<br>

<textarea name="description"
          class="form-control"></textarea>

</div>

<br>

<button type="submit"
        class="btn btn-primary">

Create Category

</button>

</form>

@endsection