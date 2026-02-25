<!-- Asset Selector Component -->

<div id="{{ $asset_selector_div_id ?? 'assigned_asset' }}" 
     class="form-group">
    {{-- // Wrapper div for styling
         // If custom div ID is passed use it
         // Otherwise default to "assigned_asset" --}}

    <label class="control-label">
        {{ $translated_name ?? 'Assets' }}
    </label>
    {{-- // Label text
         // Uses custom name if passed
         // Otherwise defaults to "Assets" --}}

    <select 
        class="js-asset-ajax"  
        {{-- // This class is used by Select2 JavaScript --}}

        name="{{ $fieldname ?? 'selected_assets[]' }}"
        {{-- // Field name sent to backend
             // Default = selected_assets[] (array format) --}}

        style="width:100%"
        {{-- // Makes dropdown full width --}}

        id="{{ $select_id ?? 'assigned_asset_select' }}"
        {{-- // Unique ID for targeting via JavaScript --}}

        {{ (!empty($multiple) && $multiple === true) ? 'multiple="multiple"' : '' }}
        {{-- // If multiple=true is passed, allow multi selection --}}

        {{ (!empty($required)) ? 'required' : '' }}
        {{-- // If required=true is passed, browser validation applies --}}
    >

        {{-- ===============================
             Preselected Assets Section
             =============================== --}}

        @if(isset($asset_ids) && is_array($asset_ids))
            {{-- // Check if selected asset IDs were passed from controller --}}

            @foreach($asset_ids as $asset_id)
                {{-- // Loop through each selected asset ID --}}

                @php
                    // Fetch asset from database
                    // Using model relation to avoid duplication issue
                    $asset = \App\Models\Asset::with('model')->find($asset_id);
                @endphp

                @if($asset)
                    {{-- // Only render if asset exists --}}

                    <option value="{{ $asset->id }}" selected>
                        {{-- // Mark this option as selected
                             // Display format: ASSET_TAG - MODEL_NAME --}}
                        {{ $asset->asset_tag }} - {{ $asset->model->name }}
                    </option>

                @endif
            @endforeach

        @endif

    </select>

</div>