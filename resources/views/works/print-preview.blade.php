<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Եզրակացություն թիվ {{ $work->conclusion_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 14pt;
            line-height: 1.6;
            color: black;
            margin: 0;
            padding: 20px;
        }

        .document {
            max-width: 21cm;
            margin: 0 auto;
            background: white;
            padding: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .conclusion-number {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .year {
            text-align: right;
            margin-bottom: 30px;
            font-size: 16pt;
        }

        .title {
            text-align: center;
            font-size: 16pt;
            margin-bottom: 40px;
            font-weight: bold;
        }

        .content {
            margin-bottom: 60px;
        }

        .item {
            margin-bottom: 20px;
            display: flex;
            align-items: baseline;
        }

        .item-number {
            margin-right: 10px;
            font-weight: bold;
        }

        .item-text {
            margin-right: 20px;
        }

        .item-value {
            border-bottom: 1px solid black;
            min-width: 200px;
            padding-bottom: 2px;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 80px;
        }

        .lab-rep {
            text-align: left;
        }

        .signature {
            text-align: right;
        }

        .signature-line {
            border-bottom: 1px solid black;
            width: 200px;
            /*margin-top: 20px;*/
            text-align: center;
            padding-bottom: 2px;
        }

        .signature-text {
            font-size: 12pt;
            margin-top: 5px;
        }

        @media print {
            body { margin: 0; }
            .document { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="document">
        <div class="header">
            <div class="conclusion-number">
                Եզրակացություն թիվ <strong>{{ $work->conclusion_number }}</strong>
            </div>
        </div>

        <div class="year">
            {{ date('d.m.Y') }} թ.
        </div>

        <div class="title">
            Ազդանշանային սարքի նորոգում չկատարելու վերաբերյալ
        </div>

        <div class="content">
            <div class="item">
                <span class="item-number">1.</span>
                <span class="item-text">Սարքի տեսակը</span>
                <span class="item-value">{{ $work->equipment->name ?? '' }}</span>
            </div>

            <div class="item">
                <span class="item-number">2.</span>
                <span class="item-text">Սարքի համարը</span>
                <span class="item-value">{{ $work->old_serial_number ?? '' }}</span>
            </div>

            <div class="item">
                <span class="item-number">3.</span>
                <span class="item-text">Ազդանշանային սարքը ենթակա չէ նորոգման</span>
            </div>

            <div class="item">
                <span class="item-number">4.</span>
                <span class="item-text">Այլ նշումներ</span>
                <span class="item-value"></span>
            </div>
        </div>

        <div class="footer">
            <div class="lab-rep">
                <div>{{ $position->title ?? 'Լաբորատորիայի ներկայացուցիչ' }}</div>
            </div>

            <div class="signature">
                <div>{{ $position->titleholder ?? '' }}</div>
                <div class="signature-line"></div>
                <div class="signature-text">անուն, ազգանուն</div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
