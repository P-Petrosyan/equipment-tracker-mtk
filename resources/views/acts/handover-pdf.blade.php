<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .title { font-size: 14px; font-weight: bold; margin: 5px 0; }
        .date { text-align: right; margin: 20px 0; }
        .content { margin: 20px 0; text-align: justify; line-height: 1.8; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .signature-section { margin-top: 20px; }
        .signature-line { border-bottom: 1px solid black; display: inline-block; width: 200px; margin: 0 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">ԱԿՏ Nº<span>{{ $act->act_number }}</span></div>
        <div class="title">Հանձնման-Ընդունման</div>
        <div class="title">Նորոգված ազդանշանային սարքերի</div>
    </div>

    <div class="date">
        <span style="float: right;"><span>{{ now()->format('d.m.Y') }}</span>թ.</span>
    </div>

    <div class="content">
        <p style="margin-left: 10px">Մենք, ներքոստորագրողներս՝</p>
        <span style="margin-left: 10px">«ՄՏԿ» ՓԲԸ</span> <span>{{$laborant->title}}</span> <span>{{$laborant->titleholder}}</span>-ը և
        <br>
        <span>{{ $act->partner->region }}</span> _____________________________________________________________________ կազմեցինք սույն ակտը առ  այն,
        որ կից ակտով(երով) ՝ նորոգումից hետ են վերադարձվում`
    </div>

    <div style="margin: 20px 0;">
        <span>{{ $act->works[0]->equipment->name }}</span> տեսակի  <span>{{ $act->works->count() }}</span> հատ ազդանշանային սարք(եր)
    </div>

    <table>
        <thead>
            <tr>
                <th>Nº</th>
                <th style="text-align: center">Գործարանային համարը</th>
                <th style="text-align: center">Ծանոթություն</th>
            </tr>
            <tr>
                <th colspan="3" style="text-align: center">Ազդանշանային սարքեր</th>
            </tr>
        </thead>
        <tbody>
            @foreach($act->works as $index => $work)
            <tr>
                <td>{{ $index + 1 }}.</td>
                <td>{{ $work->new_serial_number ?? '' }}</td>
                <td>{{ $work->defects_description ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section">
        <p>Հանձնեց՝   <span>{{$laborant->title}}</span> ___________________ <span>{{$laborant->titleholder}}</span></p>
        <br>
        <div>
            <p>Ստացավ՝ <span>{{ $act->partner->region }}</span> ներկայացուցիչ ՝ ________________________________</p>
            <p style="text-align: center; font-size: 8px;">անուն, ազգանուն</p>
        </div>
    </div>
</body>
</html>
