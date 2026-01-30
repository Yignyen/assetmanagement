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
        .action-create { color: green; font-weight: bold; }
        .action-assign { color: blue; font-weight: bold; }
        .action-update { color: orange; font-weight: bold; }
        .action-delete { color: red; font-weight: bold; }
    </style>
</head>
<body>

    <h2>Action Logs</h2>

<table>
    <thead>
        <tr>
            <th>s.no</th>
            <th>Action</th>
            <th>Performed By</th>
            <th>Item</th>
            <th>Target</th>
            <th>Note</th>
            <th>Qty</th>
            <th>Action Date</th>
        </tr>
    </thead>

    <tbody>
        @forelse($logs as $log)
            <tr>
                <td>{{ $loop->iteration }}</td>

                <td class="action-{{ $log->action_type }}">
                    {{ ucfirst($log->action_type) }}
                </td>

                <td>
                    {{ optional($log->actor)->name ?? 'System' }}
                </td>

                <td>
                    @if($log->item)
                        {{ $log->item->name ?? 'Unnamed Item' }}
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

                <td>
                    {{ $log->action_date?->format('d M Y, h:i A') }}
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