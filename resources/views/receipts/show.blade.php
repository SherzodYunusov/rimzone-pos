<!DOCTYPE html>
<html lang="uz">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chek #{{ str_pad($sale->id, 4, '0', STR_PAD_LEFT) }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

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

    @page { size: 80mm auto; margin: 0; }
    @media print {
        html, body { width: 80mm; margin: 0 !important; padding: 3mm !important; }
    }

    *, *::before, *::after { font-weight: 700 !important; }

    /* ── Utility ── */
    .center  { text-align: center; }
    .right   { text-align: right; }
    .spacer  { height: 2mm; }
    .divider       { border: none; border-top: 1px dashed #000; margin: 3mm 0; }
    .divider-solid { border: none; border-top: 2px solid #000; margin: 3mm 0; }
    .divider-thick { border: none; border-top: 3px solid #000; margin: 3mm 0; }

    /* ── Header ── */
    .logo     { font-size: 28px; font-weight: 900 !important; letter-spacing: 6px; }
    .logo-sub { font-size: 11px; letter-spacing: 2px; margin-top: 1.5mm; }

    /* ── Meta qatorlar ── */
    .meta-row {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        margin-bottom: 1.5mm;
    }

    /* ── Mahsulot qatori (2 qatorli) ── */
    .item {
        padding: 2.5mm 0;
        border-bottom: 1px dashed #ccc;
    }
    .item:last-child { border-bottom: none; }

    /* 1-qator: mahsulot nomi — to'liq kenglik */
    .item-name {
        font-size: 13px;
        font-weight: 900 !important;
        word-break: break-word;
        line-height: 1.3;
        margin-bottom: 1mm;
    }

    /* 2-qator: soni × narxi  |  jami */
    .item-calc {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
    }
    .item-formula { color: #333; }   /* soni × narxi */
    .item-total   { font-size: 13px; font-weight: 900 !important; } /* = jami */

    /* ── Grand total ── */
    .grand-block {
        border: 3px solid #000;
        padding: 3mm 3.5mm;
        margin: 2mm 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .grand-label { font-size: 13px; font-weight: 900 !important; }
    .grand-value { font-size: 21px; font-weight: 900 !important; }

    /* ── Badge ── */
    .badge {
        display: inline-block;
        border: 2px solid #000;
        padding: 1.5mm 4mm;
        font-size: 12px;
        font-weight: 900 !important;
        border-radius: 2mm;
        letter-spacing: 0.5px;
    }

    /* ── Footer ── */
    .footer-thanks { font-size: 14px; font-weight: 900 !important; letter-spacing: 1px; }
    .footer-small  { font-size: 10px; }
</style>
</head>
<body>

{{-- ══ HEADER ══ --}}
<div class="center">
    <div class="logo">RIMzone</div>
    <div class="logo-sub">SAVDO BOSHQARUV TIZIMI</div>
</div>

<hr class="divider-thick" style="margin-top:3mm;">

{{-- ══ META ══ --}}
<div class="meta-row">
    <span>CHEK: <strong>#{{ str_pad($sale->id, 4, '0', STR_PAD_LEFT) }}</strong></span>
    <span>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d.m.Y') }}</span>
</div>
<div class="meta-row">
    <span>VAQT: {{ now()->format('H:i') }}</span>
    <span>SOTUVCHI: ADMIN</span>
</div>
@if($sale->customer)
<div class="meta-row">
    <span>MIJOZ:</span>
    <span style="text-align:right; max-width:60%;">
        {{ mb_strtoupper($sale->customer->name) }}
        @if($sale->customer->company_name)
            / {{ $sale->customer->company_name }}
        @endif
    </span>
</div>
@endif

<hr class="divider-solid">
<div style="font-size:11px; font-weight:900 !important; letter-spacing:1px; margin-bottom:2mm;">
    MAHSULOTLAR ({{ $sale->items->count() }} xil):
</div>

{{-- ══ MAHSULOTLAR — 2 QATORLI FORMAT ══ --}}
<div>
    @foreach($sale->items as $item)
    <div class="item">
        {{-- 1-qator: nom --}}
        <div class="item-name">{{ $item->product ? $item->product->name : '— o\'chirilgan —' }}</div>
        {{-- 2-qator: son × narx = jami --}}
        <div class="item-calc">
            <span class="item-formula">
                {{ $item->quantity }} x {{ number_format($item->unit_price, 0, '.', ' ') }} so'm
            </span>
            <span class="item-total">= {{ number_format($item->unit_price * $item->quantity, 0, '.', ' ') }} so'm</span>
        </div>
    </div>
    @endforeach
</div>

{{-- ══ JAMI ══ --}}
<hr class="divider-solid">
<div class="grand-block">
    <span class="grand-label">JAMI SUMMA:</span>
    <span class="grand-value">{{ number_format($sale->total_price, 0, '.', ' ') }} so'm</span>
</div>

{{-- ══ TO'LOV USULI ══ --}}
@php
    $payLabels = ['naqd' => 'NAQD PUL', 'karta' => 'KARTA', 'nasiya' => 'NASIYA'];
    $payLabel  = $payLabels[$sale->payment_method] ?? mb_strtoupper($sale->payment_method);
@endphp
<div style="margin: 2mm 0 3mm;">
    <span class="badge">{{ $payLabel }}</span>
    @if($sale->payment_method === 'nasiya' && $sale->due_date)
    <span style="font-size:11px; margin-left:2mm;">
        Muddat: {{ \Carbon\Carbon::parse($sale->due_date)->format('d.m.Y') }}
    </span>
    @endif
</div>

<hr class="divider">

{{-- ══ FOOTER ══ --}}
<div class="center" style="margin-top:2mm;">
    <div class="footer-small">Sotuvchi: Administrator</div>
    <div class="spacer"></div>
    <div class="spacer"></div>
    <div class="footer-thanks">★ RAHMAT! ★</div>
    <div class="spacer"></div>
    <div class="footer-small" style="margin-top:1.5mm;">rimzone.uz</div>
</div>

</body>
</html>
