<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { margin-bottom: 20px; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
        .page-break { page-break-after: always; }
        h1 { font-size: 18px; margin-bottom: 5px; }
        h2 { font-size: 16px; margin-top: 15px; margin-bottom: 5px; }
        h3 { font-size: 14px; margin-top: 10px; margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Repair Report: {{ $partner->name }}</h1>
        <p>Period: {{ $startDate }} to {{ $endDate }}</p>
        <p>Generated: {{ date('Y-m-d H:i') }}</p>
    </div>

    @foreach($equipment as $item)
        <div style="margin-bottom: 20px; border: 1px solid #000; padding: 10px;">
            <h2>Equipment: {{ $item->model }} ({{ $item->serial_number }})</h2>
            <p><strong>ID:</strong> {{ $item->internal_id ?? '-' }} | <strong>Status:</strong> {{ $item->status->name }} | <strong>Received:</strong> {{ $item->received_at ? $item->received_at->format('Y-m-d') : '-' }}</p>
            
            @if($item->workOrders->count() > 0)
                <h3>Work Performed</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Technician</th>
                            <th>Labor Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($item->workOrders as $work)
                            <tr>
                                <td>{{ $work->end_date ? $work->end_date->format('Y-m-d') : '-' }}</td>
                                <td>{{ $work->description }}</td>
                                <td>{{ $work->technician_name }}</td>
                                <td>{{ number_format($work->labor_cost, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @php 
                $allParts = $item->parts->concat($item->workOrders->flatMap->parts);
            @endphp

            @if($allParts->count() > 0)
                <h3>Parts Used</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Part</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allParts as $part)
                            <tr>
                                <td>{{ $part->name }}</td>
                                <td>{{ $part->quantity }}</td>
                                <td>{{ number_format($part->unit_price, 2) }}</td>
                                <td>{{ number_format($part->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <div style="text-align: right; margin-top: 10px;">
                <p>Labor: {{ number_format($item->total_labor, 2) }} | Parts: {{ number_format($item->total_parts, 2) }}</p>
                <p><strong>Total for Equipment: {{ number_format($item->grand_total, 2) }}</strong></p>
            </div>
        </div>
    @endforeach

    <div class="page-break"></div>

    <div class="summary">
        <h1>Summary</h1>
        <table>
            <tr>
                <th>Total Equipment Count</th>
                <td>{{ $equipment->count() }}</td>
            </tr>
            <tr>
                <th>Total Labor Cost</th>
                <td>{{ number_format($totalLabor, 2) }}</td>
            </tr>
            <tr>
                <th>Total Parts Cost</th>
                <td>{{ number_format($totalParts, 2) }}</td>
            </tr>
            <tr class="total-row">
                <th>Grand Total</th>
                <td>{{ number_format($grandTotal, 2) }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
