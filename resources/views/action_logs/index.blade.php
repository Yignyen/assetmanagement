<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

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
            background-color: #f5f5f5;
        }
        .action-create   { color: green; font-weight: bold; }
        .action-checkout { color: blue; font-weight: bold; }
        .action-checkin  { color: purple; font-weight: bold; }
        .action-update   { color: orange; font-weight: bold; }
        .action-delete   { color: red; font-weight: bold; }

    </style>
</head>
<body>
    <a href="{{ route('assets.index') }}" class="btn btn-warning">
    ← Back to Assets
</a>
<br>
<br>


    <h2>Action Logs</h2>

<table>
    <thead>
        <tr>
            <th>s.no</th>
            <th>Created At</th>
            <th>Created By</th>
            <th>Action</th>
            <th>Item</th>
            <th>Target</th>
            <th>Note</th>
            <th>Qty</th>
           
        </tr>
    </thead>

    <tbody>
        @forelse($logs as $log)
            <tr>
                <td>{{ $loop->iteration }}</td>

                <td>
                    {{ $log->action_date?->format('d M Y, h:i A') }}
                </td>
                
                <td>
                    {{ optional($log->actor)->name ?? 'System' }}
                </td>

                <td class="action-{{ $log->action_type }}">
                    {{ ucfirst($log->action_type) }}
                </td>

                <td>
                    @if($log->item)
                {{ 
                    '#' . ($log->item->serial_no ?? '—') 
                        . ' - ' . ($log->item->model?->name ?? 'Unknown Model')
                }}
                @else
                        —
                @endif
                </td>

                <td>
                    @if($log->target)
                        {{ $log->target->name ?? 'unknown user'}}
                    @else
                        —
                    @endif
                </td>

                <td>
                    {{ $log->note ?? '—' }}
                </td>

                <td>
                    {{ $log->quantity }}
                </td>

                
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align:center;">
                    No action logs found
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
    
</body>
</html>