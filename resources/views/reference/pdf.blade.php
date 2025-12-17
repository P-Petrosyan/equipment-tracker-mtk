<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Հաշվետվություն</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            margin: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .date {
            text-align: right;
            margin-bottom: 15px;
            font-size: 10px;
        }
        .acts-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .acts-table th,
        .acts-table td {
            border: 1px solid black;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
            font-size: 9px;
        }
        .acts-table th {
            font-weight: bold;
        }
        .totals {
            margin-top: 20px;
            display: table;
            width: 100%;
        }
        .signature-section {
            display: table-row;
        }
        .signature-left,
        .signature-right {
            display: table-cell;
            width: 50%;
            padding: 10px;
            vertical-align: top;
        }
    </style>
</head>
<body>
    <div style="margin-bottom: 10px;">
        Համաձայն <span>{{ $naming->text }}</span> պայմանագրի
    </div>
    <div class="header">
        <div class="title">ՏԵՂԵԿԱՆՔ</div>
        <div class="title">«ՄՏԿ» ՓԲԸ-ում գազի ազդանշանային սարքերի նորոգման վերաբերյալ</div>
        <div class="title"><span>{{ $armenianMonths[\Carbon\Carbon::parse($endDate)->format('F')] . ' '. \Carbon\Carbon::parse($endDate)->format('Y') . 'թ.' }}</span></div>
    </div>

    <div class="date">
        <span style="float: right;"><span>{{ \Carbon\Carbon::parse($endDate)->format('d.m.Y') }}</span>թ.</span>
    </div>

    @if($acts->count() > 0)
        <table class="acts-table">
            <thead>
            <tr>
                <th rowspan="4">Հ/Հ</th>
                <th rowspan="4">ԳԳՄ անվանումը</th>
                <th rowspan="4">Կատարողական Ակտի համար</th>
                <th colspan="4">Ազդանշանային սարքեր</th>
            </tr>
            <tr>
                <th rowspan="3">Ընդամենը</th>
                <th colspan="3">որից՝</th>
            </tr>
            <tr>
                <th>Վերադարձ</th>
                <th colspan="2">Վերանորոգում՝</th>
            </tr>
            <tr>
                <th>քանակ (հատ)</th>
                <th>քանակ (հատ)</th>
                <th>գումար (ներառյալ ԱԱՀ)</th>
            </tr>
            </thead>
            <tbody>
                @php
                    $totalCount = 0;
                    $totalReturn = 0;
                    $totalRepair = 0;
                    $totalAmount = 0;
                @endphp
                @foreach($acts as $index => $act)
                    @php
                        $actTotal = $act->works->count();
                        $actReturn = $act->works->where('non_repairable', 1)->count();
                        $actRepair = $act->works->where('non_repairable', 0)->count();
                        $actAmount = $act->works->sum('equipment_part_group_total_price') * 1.2;

                        $totalCount += $actTotal;
                        $totalReturn += $actReturn;
                        $totalRepair += $actRepair;
                        $totalAmount += $actAmount;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $act->partner->region }}</td>
                        <td>{{ $act->act_number }}</td>
                        <td>{{ $actTotal }}</td>
                        <td>{{ $actReturn }}</td>
                        <td>{{ $actRepair }}</td>
                        <td>{{ number_format($actAmount, 1) }}</td>
                    </tr>
                @endforeach
                <tr style="font-weight: bold;">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ $totalCount }}</td>
                    <td>{{ $totalReturn }}</td>
                    <td>{{ $totalRepair }}</td>
                    <td>{{ number_format($totalAmount, 1) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="totals">
            <div class="signature-section">
                <div class="signature-left">
                    <div>Կատարող</div>
                    <div>«ՄՏԿ» ՓԲԸ</div>
                    <div>{{$tnoren->title}}</div>
                    <div style="margin-top: 10px">__________________________ Ա. Եփրեմյան</div>
                </div>
                <div class="signature-right">
                    <div>Պատվիրատու</div>
                    <div>«Գազպրոմ Արմենիա» ՓԲԸ</div>
                    <div>{{$vachNaxagah->name}}</div>
                    <div style="margin-top: 10px">__________________________ {{ $vachNaxagah->text }}</div>
                </div>
            </div>
        </div>
    @else
        <p>Ընտրված ժամանակահատվածում ակտեր չկան</p>
    @endif
</body>
</html>
