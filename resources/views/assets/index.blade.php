<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
     <title>Assets List</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #f5f5f5;
        }
        .status-available { color: green; font-weight: bold; }
        .status-assigned  { color: blue; font-weight: bold; }
        
    </style>
</head>
<body>
    
    
<h2>Assets</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Asset Name</th>
            <th>Serial No</th>
            <th>Asset Tag</th>
            <th>Category</th>
            <th>Status</th>
            <th>Assigned To</th>
            <th>Purchase Date</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @forelse($assets as $asset)
            <tr>
                <td>{{ $asset->id }}</td>
                <td>{{ $asset->name }}</td>
                <td>{{ $asset->serial_no }}</td>
                <td>{{ $asset->asset_tag }}</td>
                <td>{{ $asset->category->name ?? '—' }}</td>

                 <td class="status-{{ $asset->status }}">
                    {{ ucfirst($asset->status) }} {{-- //makes it captial furst letter  --}}
                </td>
 
              {{--   <td class="status-{{ $asset->assigned ? $asset->status : 'available' }}">  If $asset->assigned exists → use $asset->status Otherwise → use 'available'”--}}

                   {{--  {{ $asset->assigned ? ucfirst($asset->status) : 'Available' }}{{-- $asset->assigned → relationship,Returns:User object → TRUE,null → FALSE --}}
                {{-- </td>  If this asset has a real assigned user → show real status
Otherwise → force status to Available” --}} 


                <td> {{-- assigned relation method name --}}
                    @if($asset->assigned)    {{-- eloguent relationship, in asset model, function assigned()-> returns a user object or null --}}
                        {{ $asset->assigned->name }}
                       {{-- Finds a method called assigned(),Executes the relationship query Returns: a User model ✅ or null --}}

                    @else
                        —
                    @endif
                </td>

                <td>
                    {{ $asset->purchase_date ?? '—' }}
                </td>

                <td>
                    <a href="{{ route('assets.show', $asset) }}">
                        View / Update
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="text-align:center;">
                    No assets found
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
    
</body>
</html>