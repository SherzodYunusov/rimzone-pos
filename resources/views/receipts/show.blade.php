<!DOCTYPE html>
<html lang="uz">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chek #{{ str_pad($sale->id, 4, '0', STR_PAD_LEFT) }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    /* Hamma matn qalin — termal printer uchun */
    body {
        font-family: 'Courier New', Courier, monospace;
        font-size: 13px;
        font-weight: 700;
        width: 80mm;
        margin: 0 auto;
        background: #fff;
        color: #000;
        padding: 4mm;
    }

    @page {
        size: 80mm auto;
        margin: 0;
    }
    @media print {
        html, body {
            width: 80mm;
            margin: 0 !important;
            padding: 3mm !important;
        }
    }

    /* Barcha elementlar qalin */
    *, *::before, *::after {
        font-weight: 700 !important;
    }

    .center { text-align: center; }
    .right  { text-align: right; }

    .rimzone-logo {
        font-size: 26px;
        font-weight: 900 !important;
        letter-spacing: 5px;
        line-height: 1.1;
    }
    .rimzone-sub {
        font-size: 11px;
        font-weight: 700 !important;
        letter-spacing: 1px;
        margin-top: 1.5mm;
    }

    .divider       { border: none; border-top: 1px dashed #000; margin: 3mm 0; }
    .divider-solid { border: none; border-top: 2px solid #000; margin: 3mm 0; }
    .divider-thick { border: none; border-top: 3px solid #000; margin: 3mm 0; }
    .spacer { height: 2mm; }

    /* Meta info */
    .meta-row {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        font-weight: 700 !important;
        margin-bottom: 1.5mm;
    }

    /* Table */
    table { width: 100%; border-collapse: collapse; }
    table th, table td {
        padding: 2mm 0;
        vertical-align: top;
        line-height: 1.4;
        font-weight: 700 !important;
    }
    table th {
        font-size: 11px;
        font-weight: 900 !important;
        text-transform: uppercase;
        border-bottom: 2px solid #000;
        padding-bottom: 2mm;
        letter-spacing: 0.5px;
    }
    table td { font-size: 12px; }

    .col-name  { width: 46%; }
    .col-qty   { width: 12%; text-align: center; }
    .col-price { width: 22%; text-align: right; }
    .col-sum   { width: 20%; text-align: right; }
    .item-name { word-break: break-word; }

    /* Jami */
    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        padding: 2mm 0;
    }
    .total-label { font-size: 12px; font-weight: 700 !important; }
    .total-value { font-size: 13px; font-weight: 900 !important; }

    /* Grand total — alohida ajralib tursin */
    .grand-block {
        background: #000;
        color: #fff;
        padding: 2.5mm 3mm;
        margin: 2mm 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .grand-block * { color: #fff !important; }
    .grand-label { font-size: 13px; font-weight: 900 !important; }
    .grand-value { font-size: 17px; font-weight: 900 !important; }

    /* Badge */
    .badge {
        display: inline-block;
        border: 2px solid #000;
        padding: 1.5mm 4mm;
        font-size: 12px;
        font-weight: 900 !important;
        border-radius: 2mm;
        letter-spacing: 0.5px;
    }

    /* Footer */
    .footer-seller { font-size: 11px; font-weight: 700 !important; }
    .footer-thanks { font-size: 13px; font-weight: 900 !important; letter-spacing: 1px; }
    .footer-url    { font-size: 10px; font-weight: 700 !important; }
</style>
</head>
<body>

    {{-- HEADER --}}
    <div class="center">
        <div class="rimzone-logo">RIMzone</div>
        <div class="rimzone-sub">SAVDO BOSHQARUV TIZIMI</div>
    </div>

    <hr class="divider-thick" style="margin-top:3mm;">

    {{-- META --}}
    <div class="meta-row">
        <span>CHEK: #{{ str_pad($sale->id, 4, '0', STR_PAD_LEFT) }}</span>
        <span>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d.m.Y') }}</span>
    </div>
    <div class="meta-row" style="font-size:11px;">
        <span>VAQT: {{ now()->format('H:i') }}</span>
        <span>SOTUVCHI: ADMIN</span>
    </div>
    @if($sale->customer)
    <div class="meta-row">
        <span>MIJOZ:</span>
        <span>{{ mb_strtoupper($sale->customer->name) }}@if($sale->customer->company_name) / {{ $sale->customer->company_name }}@endif</span>
    </div>
    @endif

    <hr class="divider">

    {{-- MAHSULOTLAR --}}
    <table>
        <thead>
            <tr>
                <th class="col-name">Mahsulot</th>
                <th class="col-qty">Son</th>
                <th class="col-price">Narx</th>
                <th class="col-sum">Jami</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td class="col-name item-name">{{ $item->product ? $item->product->name : '—' }}</td>
                <td class="col-qty center">{{ $item->quantity }}</td>
                <td class="col-price">{{ number_format($item->unit_price, 0, '.', ' ') }}</td>
                <td class="col-sum">{{ number_format($item->unit_price * $item->quantity, 0, '.', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- GRAND TOTAL --}}
    <div class="grand-block">
        <span class="grand-label">JAMI SUMMA:</span>
        <span class="grand-value">{{ number_format($sale->total_price, 0, '.', ' ') }} SO'M</span>
    </div>

    {{-- TO'LOV USULI --}}
    @php
        $payLabels = ['naqd' => 'NAQD PUL', 'karta' => 'KARTA', 'nasiya' => 'NASIYA'];
        $payLabel  = $payLabels[$sale->payment_method] ?? mb_strtoupper($sale->payment_method);
    @endphp
    <div style="margin: 2mm 0;">
        <span class="badge">{{ $payLabel }}</span>
        @if($sale->payment_method === 'nasiya' && $sale->due_date)
        <span style="font-size:11px; margin-left:2mm;">
            Muddat: {{ \Carbon\Carbon::parse($sale->due_date)->format('d.m.Y') }}
        </span>
        @endif
    </div>

    <hr class="divider">

    {{-- FOOTER --}}
    <div class="center" style="margin-top:2mm;">
        <div class="footer-seller">Sotuvchi: Administrator</div>
        <div class="spacer"></div>
        <div class="spacer"></div>
        <div class="footer-thanks">*** RAHMAT! ***</div>
        <div class="spacer"></div>
        <div class="footer-url" style="margin-top:1.5mm;">rimzone.uz</div>
    </div>

</body>
</html>
