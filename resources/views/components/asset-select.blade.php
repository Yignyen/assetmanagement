<!-- Asset Selector (Snipe-Style Adapted) -->

<div id="{{ $asset_selector_div_id ?? 'assigned_asset' }}"
     class="form-group">

    <label class="control-label">
        {{ $translated_name ?? 'Assets' }}
    </label>

    <select class="js-asset-ajax"
            name="{{ $fieldname ?? 'selected_assets[]' }}"
            style="width:100%"
            id="{{ $select_id ?? 'assigned_asset_select' }}"
            {{ (!empty($multiple) && $multiple === true) ? 'multiple' : '' }}
            {{ (!empty($required)) ? 'required' : '' }}
    >

        {{-- Preselected assets (from flash old input) --}}
        @if(isset($asset_ids))
            @foreach($asset_ids as $asset_id)
                @php
                    $asset = \App\Models\Asset::find($asset_id);
                @endphp

                @if($asset)
                    <option value="{{ $asset->id }}" selected>
                        {{ $asset->asset_tag }} - {{ $asset->name }}
                    </option>
                @endif
            @endforeach
        @endif

    </select>

</div>