@extends('layouts.admin')

@section('title', 'Bulk Restore')
@section('page-title', 'Bulk Restore')

@section('content')

<a href="{{ route('assets.index') }}" class="btn btn-warning">
    ← Back to Assets
</a>

<hr>

{{-- <h3>Bulk Assign ({{ count($assets) }} selected)</h3> --}}

@if(session('warning') && session('removed_assets'))
    <div class="alert alert-warning">

        <strong>The following assets were removed (not deployable or already assigned):</strong>

        <ul>
            @foreach(session('removed_assets') as $tag)
                <li>{{ $tag }}</li>
            @endforeach
        </ul>

        @if(session('remaining_assets') && count(session('remaining_assets')))
            <strong>Remaining assets ready for checkout:</strong>
            <ul>
                @foreach(session('remaining_assets') as $tag)
                    <li>{{ $tag }}</li>
                @endforeach
            </ul>
        @else
            <strong>No valid assets remain selected. You may add new ones.</strong>
        @endif

    </div>
@endif
<hr>

{{-- =====================
   BULK CHECKOUT FORM
===================== --}}

<form method="POST" action="{{ route('assets.bulk.restore.process') }}">
    @csrf

    @include('components.asset-select', [
       'translated_name' => 'Deleted Assets',
       'fieldname' => 'selected_assets[]',
       'multiple' => true,
       'required' => true,
       'select_id' => 'restore_assets_select',
       'asset_selector_div_id' => 'assets_to_restore_div',
       'asset_ids' => [],
       /* 'allAssets' => $allAssets, */
    ])

    <br>

    <button type="submit" class="btn btn-success">
        Restore Selected
    </button>
</form>





{{-- </script> --}}

@section('scripts')
<script>
$('#restore_assets_select').select2({
    ajax: {
        url: '/ajax/deleted-assets',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return { q: params.term };
        },
        processResults: function (data) {
            return data; // because we already returned { results: [...] }
        }
    },
    minimumInputLength: 1,
    placeholder: 'Search asset...',
    allowClear: true,
    width: '100%'
});
</script>
@endsection




@endsection