<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Struk</title>

<style>
body{
    font-family: 'Segoe UI', sans-serif;
    background:#fff;
    color:#111;
    margin:0;
}

.container{
    max-width:320px;
    margin:auto;
    padding:12px;
}

/* HEADER */
.header{
    text-align:center;
    margin-bottom:8px;
}

.header h1{
    font-size:16px;
    margin:0;
    font-weight:700;
    letter-spacing:1px;
}

.header p{
    font-size:11px;
    margin:2px 0;
    color:#555;
}

/* LINE */
.line{
    border-top:1px dashed #ccc;
    margin:8px 0;
}

/* INFO */
.info{
    font-size:12px;
}

.row{
    display:flex;
    justify-content:space-between;
    margin:2px 0;
}

/* TABLE */
.table{
    width:100%;
    font-size:12px;
}

.table th{
    text-align:left;
    font-weight:600;
    padding-bottom:4px;
}

.table td{
    padding:2px 0;
}

.right{
    text-align:right;
}

.center{
    text-align:center;
}

/* TOTAL */
.total{
    font-size:12px;
}

.total .bold{
    font-weight:700;
}

.total .grand{
    font-size:14px;
    font-weight:800;
}

/* FOOTER */
.footer{
    text-align:center;
    font-size:11px;
    color:#555;
    margin-top:6px;
}

/* PRINT BUTTON */
.print-btn{
    display:block;
    width:100%;
    margin-top:10px;
    padding:6px;
    background:#111;
    color:#fff;
    border:none;
    border-radius:4px;
    cursor:pointer;
}

@media print{
    .print-btn{
        display:none;
    }
    body{
        margin:0;
    }
}
</style>

</head>

<body>

<div class="container">

<!-- HEADER -->
<div class="header">
    <h1>WICFUL STORE</h1>
    <p>Jl. Raya Denpasar No.123</p>
    <p>Denpasar, Bali</p>
    <p>Telp: 0812-3456-7890</p>
</div>

<div class="line"></div>

<!-- INFO -->
<div class="info">
    <div class="row">
        <span>Invoice</span>
        <span>{{ $trx->invoice }}</span>
    </div>
    <div class="row">
        <span>Tanggal</span>
        <span>{{ $trx->created_at->format('d/m/Y H:i') }}</span>
    </div>
    <div class="row">
        <span>Metode</span>
        <span>{{ strtoupper($trx->method) }}</span>
    </div>

    @if($trx->midtrans_id)
    <div style="margin-top:4px;font-size:10px;word-break:break-all;">
        ID: {{ $trx->midtrans_id }}
    </div>
    @endif
</div>

<div class="line"></div>

<!-- TABLE -->
<table class="table">

<thead>
<tr>
<th>Item</th>
<th class="center">Qty</th>
<th class="right">Harga</th>
</tr>
</thead>

<tbody>

@php
$subtotalAll = 0;
$discountAll = 0;
@endphp

@foreach($trx->items as $item)

@php
$disc = $item->price * $item->discount / 100;
$final = $item->price - $disc;
$subtotal = $final * $item->qty;

$subtotalAll += $item->price * $item->qty;
$discountAll += $disc * $item->qty;
@endphp

<tr>
<td>{{ $item->name }}</td>
<td class="center">{{ $item->qty }}</td>
<td class="right">Rp {{ number_format($final) }}</td>
</tr>

<tr>
<td colspan="3" class="right" style="font-size:11px;color:#666;">
Rp {{ number_format($subtotal) }}
</td>
</tr>

@endforeach

</tbody>

</table>

<div class="line"></div>

<!-- TOTAL -->
<div class="total">

<div class="row">
    <span>Subtotal</span>
    <span>Rp {{ number_format($subtotalAll) }}</span>
</div>

<div class="row">
    <span>Diskon</span>
    <span>- Rp {{ number_format($discountAll) }}</span>
</div>

<div class="row grand">
    <span>Total</span>
    <span>Rp {{ number_format($trx->total) }}</span>
</div>

<div class="row">
    <span>Bayar</span>
    <span>Rp {{ number_format($trx->pay) }}</span>
</div>

@if($trx->method === 'Cash')
<div class="row bold">
    <span>Kembali</span>
    <span>Rp {{ number_format($trx->change) }}</span>
</div>
@endif

</div>

<div class="line"></div>

<!-- FOOTER -->
<div class="footer">
    <p>Terima kasih 🙏</p>
    <p>Barang tidak dapat dikembalikan</p>
</div>

<button onclick="window.print()" class="print-btn">
Print Struk
</button>

</div>

<script>
window.onload = () => setTimeout(()=>window.print(),300)
</script>

</body>
</html>