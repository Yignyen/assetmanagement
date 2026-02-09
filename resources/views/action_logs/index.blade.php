@extends('layouts.admin')

@section('title', 'Action_logs')
@section('page-title', 'Action_logs')

@section('content')

<div class="table-scroll">
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
    </div>
@endsection
