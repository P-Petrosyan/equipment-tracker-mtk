<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ակտ {{ $act->act_number }}</title>
    <style>
        @page { margin: 15px 40px 15px 70px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; line-height: 1.3; }
        .underline { border-bottom: 1px solid #000; display: inline-block; min-width: 80px; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .content { margin: 15px 0; text-align: justify; }
        .works-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .works-table th, .works-table td { border: 1px solid #000; padding: 4px; text-align: center; font-size: 10px; }
        .works-table th { font-weight: bold; }
        .footer { margin-top: 30px; }
    </style>
</head>
<body>
    <div style="margin-bottom: 10px;">
        Համաձայն <span>{{ $naming->text }}</span> պայմանագրի
    </div>

    <div class="text-center bold" style="margin: 15px 0; font-size: 14px;">
        ԱԿՏ Nº<span>{{ $act->act_number }}</span>
    </div>

    <div class="text-center bold" style="margin: 15px 0;">
        Կատարողական աշխատանքների Հանձնման-Ընդունման
    </div>

    <div style="margin-bottom: 15px;">
        ք.Երևան <span style="float: right;"><span>{{ now()->format('d.m.Y') }}</span>թ.</span>
    </div>

    <div class="content">
        Մենք. ներքոստորագրողներս՝ «Գազպրոմ Արմենիա» ՓԲԸ <span>{{ $act->partner->region }}</span> տնօրեն՝
        <span>{{ trim(substr($act->partner->tnoren, strrpos($act->partner->tnoren, '_') + 1)) }}</span> մի կողմից, «ՄՏԿ» ՓԲԸ տնօրեն՝ <span>{{$tnoren->titleholder}}</span>  մյուս կողմից,
        կազմեցինք սույն ակտը առ այն, որ ԳԳՄ -ի ակտերով «ՄՏԿ» ՓԲԸ -ին հանձնված
        <span>{{ $act->works->count() }}</span> հատ գազի ազդանշանային սարքերը  հետ են վերադարձվել, որից <span>{{ $repairedWorks->count() }}</span> հատ
        նորոգված, <span>{{ $nonRepairedWorks->count() }}</span> հատ վերանորոգման ոչ ենթակա
        (հիմք՝ եզրակացություն <span>@foreach($nonRepairedWorks as $work)Nº{{ $work->conclusion_number }}@if(!$loop->last); @endif @endforeach</span>)
    </div>

    <table class="works-table">
        <thead>
            <tr>
                <th rowspan="4" style="width: 8%;">Հ/Հ</th>
                <th rowspan="4" style="width: 25%;">Ազդ.սարքի տեսակը/անվանումը</th>
                <th colspan="4">Ազդանշանային սարքեր</th>
            </tr>
            <tr>
                <th rowspan="3" style="width: 12%;">Ընդամենը</th>
                <th colspan="3">որից՝</th>
            </tr>
            <tr>
                <th style="width: 15%;">Վերադարձ</th>
                <th colspan="2" style="width: 15%;">Վերանորոգում</th>
            </tr>
            <tr>
                <th>քանակ (հատ)</th>
                <th style="width: 15%;">քանակ (հատ)</th>
                <th style="width: 25%;">գումար (ներառյալ ԱԱՀ)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $groupedWorks = $act->works->groupBy('equipment.name');
            @endphp
            @foreach($groupedWorks as $equipmentName => $works)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td style="text-align: left; padding-left: 8px;">{{ $equipmentName }}</td>
                <td>{{ $works->count() }}</td>
                <td>{{ $works->whereNull('conclusion_number')->count() }}</td>
                <td>{{ $works->whereNotNull('conclusion_number')->count() }}</td>
                <td>{{ number_format($works->sum('equipment_part_group_total_price') * 1.2, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="footer" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; vertical-align: top;">Կատարող</td>
            <td style="width: 50%; vertical-align: top;">Պատվիրատու</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">«ՄՏԿ» ՓԲԸ</td>
            <td style="vertical-align: top;">«Գազպրոմ Արմենիա» ՓԲԸ</td>
        </tr>
        <tr>
            <td style="vertical-align: top;"></td>
            <td style="vertical-align: top;">{{ $act->partner->region }}</td>
        </tr>
        <br>
        <tr>
            <td style="vertical-align: top;">{{ $tnoren->title . ' __________________ ' . $tnoren->titleholder}}</td>
            <td style="vertical-align: top;">{{ $act->partner->tnoren }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top;"></td>
            <td style="vertical-align: top;"><br>{{ $act->partner->hashvapah }}</td>
        </tr>
    </table>
</body>
</html>
