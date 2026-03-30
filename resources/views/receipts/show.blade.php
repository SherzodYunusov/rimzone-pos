<!DOCTYPE html>
<html lang="uz">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chek #{{ str_pad($sale->id, 4, '0', STR_PAD_LEFT) }}</title>
<style>
    /* ── Reset ── */
    * { margin: 0; padding: 0; box-sizing: border-box; }

    /* ── Screen preview (80mm kenglik) ── */
    body {
        font-family: 'Courier New', Courier, monospace;
        font-size: 12px;
        width: 80mm;
        margin: 0 auto;
        background: #fff;
        color: #000;
        padding: 4mm;
    }

    /* ── Print: brauzer sarlavhasiz, chetkasiz ── */
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

    /* ── Layout ── */
    .center   { text-align: center; }
    .right    { text-align: right; }
    .bold     { font-weight: bold; }
    .big      { font-size: 18px; }
    .medium   { font-size: 14px; }
    .small    { font-size: 10px; }
    .divider  { border: none; border-top: 1px dashed #000; margin: 3mm 0; }
    .divider-solid { border: none; border-top: 1px solid #000; margin: 3mm 0; }
    .spacer   { height: 2mm; }

    /* ── Table ── */
    table {
        width: 100%;
        border-collapse: collapse;
    }
    table th, table td {
        padding: 1.5mm 0;
        vertical-align: top;
        line-height: 1.4;
    }
    table th {
        font-size: 10px;
        text-transform: uppercase;
        border-bottom: 1px solid #000;
        padding-bottom: 2mm;
    }
    .col-name  { width: 48%; }
    .col-qty   { width: 12%; text-align: center; }
    .col-price { width: 22%; text-align: right; }
    .col-sum   { width: 18%; text-align: right; }
    .item-name { word-break: break-word; }

    /* ── Footer area ── */
    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        padding: 1.5mm 0;
    }
    .total-label { font-size: 11px; }
    .total-value { font-size: 14px; font-weight: bold; }
    .grand-total .total-label { font-size: 13px; font-weight: bold; }
    .grand-total .total-value { font-size: 16px; }

    .badge {
        display: inline-block;
        border: 1px solid #000;
        padding: 1mm 3mm;
        font-size: 11px;
        font-weight: bold;
        border-radius: 2mm;
    }

    .thank-you {
        font-size: 12px;
        font-weight: bold;
        letter-spacing: 0.5px;
    }
</style>
</head>
<body>

    {{-- ── HEADER ── --}}
    <div class="center">
        <div class="big bold" style="letter-spacing:3px;">RIMzone</div>
        <div class="small" style="margin-top:1mm; opacity:0.7;">Savdo boshqaruv tizimi</div>
    </div>

    <hr class="divider-solid" style="margin-top:3mm;">

    {{-- ── CHEK MA'LUMOTI ── --}}
    <div style="display:flex; justify-content:space-between; font-size:10px; margin-bottom:1mm;">
        <span>Chek: <strong>#{{ str_pad($sale->id, 4, '0', STR_PAD_LEFT) }}</strong></span>
        <span>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d.m.Y') }}</span>
    </div>
    @if($sale->customer)
    <div style="font-size:10px; margin-bottom:1mm;">
        Mijoz: <strong>{{ $sale->customer->name }}</strong>
        @if($sale->customer->company_name)
            ({{ $sale->customer->company_name }})
        @endif
    </div>
    @endif

    <hr class="divider">

    {{-- ── MAHSULOTLAR JADVALI ── --}}
    <table>
        <thead>
            <tr>
                <th class="col-name">Mahsulot</th>
                <th class="col-qty">Soni</th>
                <th class="col-price">Narxi</th>
                <th class="col-sum">Jami</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td class="col-name item-name">{{ $item->product ? $item->product->name : '—' }}</td>
                <td class="col-qty center">{{ $item->quantity }}</td>
                <td class="col-price">{{ number_format($item->unit_price, 0, '.', ' ') }}</td>
                <td class="col-sum bold">{{ number_format($item->unit_price * $item->quantity, 0, '.', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <hr class="divider-solid">

    {{-- ── JAMI ── --}}
    <div class="total-row grand-total">
        <span class="total-label">JAMI SUMMA:</span>
        <span class="total-value">{{ number_format($sale->total_price, 0, '.', ' ') }} so'm</span>
    </div>

    @php
        $payLabels = ['naqd' => 'Naqd pul', 'karta' => 'Karta', 'nasiya' => 'Nasiya'];
        $payLabel  = $payLabels[$sale->payment_method] ?? $sale->payment_method;
    @endphp

    <div style="margin: 2mm 0;">
        <span class="badge">{{ $payLabel }}</span>
        @if($sale->payment_method === 'nasiya' && $sale->due_date)
            <span class="small" style="margin-left:2mm;">
                Muddat: {{ \Carbon\Carbon::parse($sale->due_date)->format('d.m.Y') }}
            </span>
        @endif
    </div>

    <hr class="divider">

    {{-- ── FOOTER ── --}}
    <div class="center" style="margin-top:2mm;">
        <div class="small">Sotuvchi: <strong>Administrator</strong></div>
        <div class="spacer"></div>
        <div class="spacer"></div>
        <div class="thank-you">★ Xaridingiz uchun rahmat! ★</div>
        <div class="spacer"></div>
        <div class="small" style="opacity:0.6; margin-top:1mm;">
            {{ now()->format('H:i') }} — rimzone.uz
        </div>
    </div>

</body>
</html>
