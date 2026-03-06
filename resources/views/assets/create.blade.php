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


{{-- CATEGORY --}}
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

<a href="{{ route('categories.create') }}"
class="btn btn-sm btn-secondary">
+ New Category
</a>

</div>
</div>


{{-- MODEL --}}
<div style="margin-bottom:15px">

<label><strong>Model</strong></label>

<div style="display:flex; gap:8px; margin-top:5px">

<select id="model-select" name="model_id" style="width:300px">
<option value="">Select Model</option>
</select>

<a href="{{ route('models.create') }}" class="btn btn-sm btn-secondary">
+ New Model
</a>

</div>
</div>


{{-- LABEL --}}
<p>
<label><strong>Asset Label (Optional)</strong></label><br>
<input type="text" name="label" value="{{ old('label') }}" placeholder="CEO Laptop">
</p>


{{-- ASSET TAG --}}
<p>
<label><strong>Asset Tag</strong></label><br>

<input
type="text"
name="asset_tag"
value="{{ old('asset_tag', \App\Services\AssetTagService::generate()) }}"
required
>

<small class="text-muted">
Asset tag is auto-generated, but editable.
</small>

</p>


{{-- SERIAL --}}
<p>
<label><strong>Serial No</strong></label><br>
<input type="text" name="serial_no" value="{{ old('serial_no') }}">
</p>


{{-- SERVICE TAG --}}
<div id="service-tag-wrapper" style="display:none;">
<p>
<label><strong>Dell Service Tag</strong></label><br>
<input type="text" name="service_tag">
</p>
</div>


{{-- PURCHASE DATE --}}
<p>
<label><strong>Purchase Date</strong></label><br>
<input type="date" name="purchase_date" value="{{ old('purchase_date') }}">
</p>


<hr>

<button type="submit" class="btn btn-primary">
Create Asset
</button>

</form>

@endsection



@section('scripts')

<script>

let modelData = {};

$('#category-select').select2({
placeholder:"Select Category",
width:'300px'
});

$('#model-select').select2({
placeholder:"Select Model",
width:'300px'
});


/* LOAD MODELS WHEN CATEGORY CHANGES */

$('#category-select').on('change', function(){

let categoryId = $(this).val();

$('#model-select').empty().append('<option>Loading...</option>');

if(!categoryId){
$('#model-select').html('<option>Select Model</option>');
return;
}

$.get('/ajax/models-by-category',{category_id:categoryId},function(models){

let options='<option value="">Select Model</option>';

modelData = {};

models.forEach(function(model){

modelData[model.id]=model;

options+=`<option value="${model.id}">${model.name}</option>`;

});

$('#model-select').html(options).trigger('change');

});

});


/* SHOW SERVICE TAG IF DELL */

$('#model-select').on('change', function(){

let modelId=$(this).val();

if(!modelId){
$('#service-tag-wrapper').hide();
return;
}

let manufacturerId=modelData[modelId].manufacturer_id;

if(manufacturerId==1){
$('#service-tag-wrapper').show();
}else{
$('#service-tag-wrapper').hide();
}

});


/* CREATE MODEL AJAX */

function createModel(){

let name=$('#modelName').val();
let categoryId=$('#category-select').val();
let manufacturerId=$('#manufacturerSelect').val();
let requireSerial=$('#requireSerial').is(':checked');

if(!categoryId){
alert("Please select a category first.");
return;
}

$.post("{{ route('models.ajax.store') }}",{

_token:"{{ csrf_token() }}",
name:name,
category_id:categoryId,
manufacturer_id:manufacturerId,
require_serial:requireSerial

},function(model){

modelData[model.id]=model;

let option=new Option(model.name,model.id,true,true);

$('#model-select').append(option).trigger('change');

closeModal();

$('#modelName').val('');

});

}

//script section for open modal

/* function openModal(){
    document.getElementById('modelName').value='';
    document.getElementById('manufacturerSelect').value='';
    document.getElementById('requireSerial').checked=false;

    document.getElementById('newModelModal').classList.add('show');
}

function closeModal(){
    document.getElementById('newModelModal').classList.remove('show');
}
 */
</script>

@endsection



{{-- MODEL MODAL --}}

{{-- <div class="modal" id="newModelModal">

<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
<h5>Create Model</h5>
<button type="button" onclick="closeModal()" style="border:none;background:none;font-size:18px;">
✕
</button>
</div>

<div class="modal-body">

<label>Model Name</label>
<input type="text" id="modelName" class="form-control">

<br>

<label>Manufacturer</label>

<select id="manufacturerSelect" class="form-control">

<option value="">Select Manufacturer</option>

@foreach($manufacturers as $manufacturer)
<option value="{{ $manufacturer->id }}">
{{ $manufacturer->name }}
</option>
@endforeach

</select>

<br>

<label>
<input type="checkbox" id="requireSerial">
Require Serial Number
</label>

</div>

<div class="modal-footer">

<button class="btn btn-secondary" data-bs-dismiss="modal">
Cancel
</button>

<button class="btn btn-secondary" onclick="closeModal()">
Cancel
</button>

</div>

</div>
</div>

</div> --}}