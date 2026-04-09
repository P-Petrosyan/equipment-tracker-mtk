<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ՍՉԱՄ Եռակողմ պայմանագիր</title>
</head>
<body>
<div style="text-align: center; margin-bottom: 30px;">
    <div style="font-size: 14px; font-weight: bold; margin-bottom: 10px;">ՀԱՍՏԱՏՈՒՄ ԵՄ</div>
    <div style="font-size: 14px; font-weight: bold; margin-bottom: 10px;">«Գազպրոմ Արմենիա» ՓԲԸ</div>
    <div>{{$glxavorChartaraget->name}}</div>
    <div style="margin-top: 10px;"> ______________________________ {{$glxavorChartaraget->text}}</div>
    <div style="margin-top: 10px;">«____» _____________ {{ \Carbon\Carbon::parse($endDate)->format('Y') }}թ.</div>
</div>

<table style="width: 100%; margin: 30px 0;">
    <tr>
        <td style="text-align: center; width: 40%; padding: 10px;">
            <div style="font-weight: bold; text-align: center;">ՀԱՍՏԱՏՈՒՄ ԵՄ</div>
            <div>«ՄՏԿ» ՓԲԸ <span>{{ $tnoren->title }}</span></div>
            <div style="margin-top: 10px;">
                _____________________ {{ $tnoren->titleholder }}
            </div>
            <div style="margin-top: 10px;">
                «____» _____________ {{ \Carbon\Carbon::parse($endDate)->format('Y') }}թ.
            </div>
        </td>
        <td style="width: 20%;"></td>
        <td style="text-align: center; width: 40%; padding: 10px;">
            <div style="font-weight: bold; text-align: center;">ՀԱՍՏԱՏՈՒՄ ԵՄ</div>
            <div>«Ստանդարտացման և չափագիտության</div>
            <div>ազգային մարմին» ՓԲԸ {{ $tnoreniTexakal->name }} </div>
            <div style="margin-top: 10px;">
                _____________________ {{ $tnoreniTexakal->text }}
            </div>
            <div style="margin-top: 10px;">
                «____» _____________ {{ \Carbon\Carbon::parse($endDate)->format('Y') }}թ.
            </div>
        </td>
    </tr>
</table>

<div style="margin: 30px 0; text-align: justify;">
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
        <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
            <thead>
            <tr>
                <th style="border: 1px solid #000; padding: 4px; text-align: center; font-size: 10px; font-weight: bold;" rowspan="2">Nº</th>
                <th style="border: 1px solid #000; padding: 4px; text-align: center; font-size: 10px; font-weight: bold;" rowspan="2">ԳԳՄ-ի անվանումը</th>
                <th style="border: 1px solid #000; padding: 4px; text-align: center; font-size: 10px; font-weight: bold;" colspan="2">HD2000</th>
            </tr>
            <tr>
                <th style="border: 1px solid #000; padding: 4px; text-align: center; font-size: 10px; font-weight: bold;">Քանակը (հատ)</th>
                <th style="border: 1px solid #000; padding: 4px; text-align: center; font-size: 10px; font-weight: bold;">Գինը (դրամ) առանց ԱԱՀ</th>
            </tr>
            </thead>
            <tbody>
            @php $totalWorks = 0; $totalPrice = 0; @endphp
            @foreach($partnerData as $index => $partner)
                <tr>
                    <td style="border: 1px solid #000; padding: 4px; text-align: center; font-size: 10px;">{{ $loop->iteration }}</td>
                    <td style="border: 1px solid #000; padding: 4px; text-align: left; padding-left: 8px; font-size: 10px;">{{ $partner['name'] }}</td>
                    <td style="border: 1px solid #000; padding: 4px; text-align: center; font-size: 10px;">{{ $partner['total_works'] }}</td>
                    <td style="border: 1px solid #000; padding: 4px; text-align: center; font-size: 10px;">{{ number_format($partner['total_price'], 0) }}</td>
                </tr>
                @php
                    $totalWorks += $partner['total_works'];
                    $totalPrice += $partner['total_price'];
                @endphp
            @endforeach
            <tr style="font-weight: bold;">
                <td style="border: 1px solid #000; padding: 4px; text-align: center; font-size: 10px;"></td>
                <td style="border: 1px solid #000; padding: 4px; text-align: center; font-size: 10px;">Ընդամենը</td>
                <td style="border: 1px solid #000; padding: 4px; text-align: center; font-size: 10px;">{{ $totalWorks }}</td>
                <td style="border: 1px solid #000; padding: 4px; text-align: center; font-size: 10px;">{{ number_format($totalPrice, 0) }}</td>
            </tr>
            </tbody>
        </table>
    </div>

    <table style="width: 100%; margin-top: 30px;">
        <tr>
            <td style="text-align: center; width: 40%; padding: 10px; vertical-align: top;">
                <div style="font-weight: bold;">«ՄՏԿ» ՓԲԸ</div>
                <div>Գազի  ձայնաազդանշանային սարքերի</div>
                <div>ստուգաչափման աջակցության</div>
                <div>{{ $labXekavar->title }}</div>
                <div>{{ $labXekavar->titleholder }}</div>
                <div style="margin-top: 10px;">
                   ______________________
                </div>
                <div>ստորագրություն</div>
            </td>
            <td style="width: 20%;"></td>
            <td style="text-align: center; width: 40%; padding: 10px; vertical-align: top;">
                <div style="font-weight: bold;">«Ստանդարտացման և չափագիտության</div>
                <div style="font-weight: bold;">ազգային մարմին» ՓԲԸ</div>
                <div>{{ $labVarich->name }}</div>
                <div>{{ $labVarich->text }}</div>
                <div></div>
                <div style="margin-top: 10px;">
                    ______________________
                </div>
                <div>ստորագրություն</div>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
