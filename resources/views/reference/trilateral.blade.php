<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ՍՉԱՄ Եռակողմ պայմանագիր</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .mt-2 {
            margin-top: 10px;
        }

        .contract-title {
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0;
        }

        .date-section {
            text-align: center;
            margin: 20px 0;
            font-size: 14px;
        }

        .parties-section {
            display: table;
            flex-direction: row;
            width: 100%;
            justify-content: space-between;
            /*margin: 30px 0;*/
        }

        .party {
            display: table-cell;
            text-align: center;
            width: 40%;
            padding: 10px;
            /*vertical-align: top;*/
            /*border: 1px solid #000;*/
        }

        .party-title {
            font-weight: bold;
            text-align: center;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            display: inline-block;
            width: 200px;
            margin: 0 5px;
        }

        .content-section {
            margin: 30px 0;
            text-align: justify;
        }

        .footer-signatures {
            margin-top: 50px;
            display: table;
            width: 100%;
        }

        .signature-block {
            display: table-cell;
            width: 33.33%;
            padding: 10px;
            vertical-align: top;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .signature-name {
            margin: 10px 0;
        }

        .works-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .works-table th, .works-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            font-size: 10px;
        }

        .works-table th {
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="header">
    <div class="company-name">ՀԱՍՏԱՏՈՒՄ ԵՄ</div>
    <div class="company-name">«Գազպրոմ Արմենիա» ՓԲԸ</div>
    <div>{{$glxavorChartaraget->name}}</div>
    <div class="mt-2"> ______________________________ {{$glxavorChartaraget->text}}</div>
    <div class="mt-2">«____» _____________ {{ \Carbon\Carbon::parse($endDate)->format('Y') }}թ.</div>
</div>

<div class="parties-section">
    <div class="party">
        <div class="party-title">ՀԱՍՏԱՏՈՒՄ ԵՄ</div>
        <div>«ՄՏԿ» ՓԲԸ <span>{{ $tnoren->title }}</span></div>
        <div class="mt-2">
            _____________________ {{ $tnoren->titleholder }}
        </div>
        <div class="mt-2">
            «____» _____________ {{ \Carbon\Carbon::parse($endDate)->format('Y') }}թ.
        </div>
    </div>

    <div class="party">
        <div class="party-title">ՀԱՍՏԱՏՈՒՄ ԵՄ</div>
        <div>«Ստանդարտացման և չափագիտության</div>
        <div>ազգային մարմին» ՓԲԸ {{ $tnoreniTexakal->name }} </div>
        <div class="mt-2">
            _____________________ {{ $tnoreniTexakal->text }}
        </div>
        <div class="mt-2">
            «____» _____________ {{ \Carbon\Carbon::parse($endDate)->format('Y') }}թ.
        </div>
    </div>
</div>

<div class="content-section">
    <div style="text-align: center; font-weight: bold; margin-bottom: 20px;">
        ԱԿՏ<br>
        ԱՇԽԱՏԱՆՔՆԵՐԻ ՀԱՆՁՆՄԱՆ-ԸՆԴՈՒՆՄԱՆ
    </div>

    <div style="margin-bottom: 15px;">
        {{ \Carbon\Carbon::parse($endDate)->format('d.m.Y') }}թ. <span style="float: right"> «ՄՏԿ» ՓԲԸ</span>
    </div>

    <div>
        Մենք` ներքոստորագրողներս, «ՄՏԿ» ՓԲԸ գազի ձայնաազդանշանային սարքերի
        ստուգաչափման աջակցության <span>{{$labXekavar->title . ' ' . $labXekavar->titleholder}}ը,</span>
        «Ստանդարտացման և չափագիտության ազգային մարմին» ՓԲԸ <span>{{ $labVarich->name . ' ' . $labVarich->text}}ը</span>
        կազմեցինք սույն ակտն այն մասին, որ <span>{{ \Carbon\Carbon::parse($endDate)->format('Y') . 'թ. ' . $armenianMonths[\Carbon\Carbon::parse($endDate)->format('F')] }}</span>
        ամսվա ընթացքում, համաձայն «Գազպրոմ Արմենիա» ՓԲԸ և «Ստանդարտացման և չափագիտության ազգային
        մարմին» ՓԲԸ միջև <span> {{ $SCHAM->text }}</span> պայմանագրի «ՄՏԿ» ՓԲԸ-ում կատարվել է HENAN
        CHICHENG ELECTRIC CO. LTD ընկերության արտադրության HD2000 տիպի
        ձայնաազդանշանային սարքերի ստուգաչափում հետևյալ քանակներով, որի համար ստորագրում ենք:
    </div>

    <div>
        <table class="works-table">
            <thead>
            <tr>
                <th rowspan="2">Nº</th>
                <th rowspan="2">ԳԳՄ-ի անվանումը</th>
                <th colspan="2">HD2000</th>
            </tr>
            <tr>
                <th>Քանակը (հատ)</th>
                <th>Գինը (դրամ) առանց ԱԱՀ</th>
            </tr>
            </thead>
            <tbody>
            @php $totalWorks = 0; $totalPrice = 0; @endphp
            @foreach($partnerData as $index => $partner)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td style="text-align: left; padding-left: 8px;">{{ $partner['name'] }}</td>
                    <td>{{ $partner['total_works'] }}</td>
                    <td>{{ number_format($partner['total_price'], 0) }}</td>
                </tr>
                @php
                    $totalWorks += $partner['total_works'];
                    $totalPrice += $partner['total_price'];
                @endphp
            @endforeach
            <tr style="font-weight: bold;">
                <td></td>
                <td>Ընդամենը</td>
                <td>{{ $totalWorks }}</td>
                <td>{{ number_format($totalPrice, 0) }}</td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="parties-section">
        <div class="party">
            <div class="party-title">«ՄՏԿ» ՓԲԸ</div>
            <div>Գազի  ձայնաազդանշանային սարքերի</div>
            <div>ստուգաչափման աջակցության</div>
            <div>{{ $labXekavar->title }}</div>
            <div>{{ $labXekavar->titleholder }}</div>
            <div class="mt-2">
               ______________________
            </div>
            <div>ստորագրություն</div>
        </div>

        <div class="party">
            <div class="party-title">«Ստանդարտացման և չափագիտության</div>
            <div class="party-title">ազգային մարմին» ՓԲԸ</div>
            <div>{{ $labVarich->name }}</div>
            <div>{{ $labVarich->text }}</div>
            <div></div>
            <div class="mt-2">
                ______________________
            </div>
            <div>ստորագրություն</div>
        </div>
    </div>
</div>
</body>
</html>
