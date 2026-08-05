<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Voucher</title>

<style>

@page{
    size:80mm auto;
    margin:3mm;
}

body{

    width:72mm;
    margin:auto;

    font-family:Arial, Helvetica, sans-serif;
    font-size:11px;

    color:#000;
}

.logo{

    text-align:center;
}

.logo img{

    width:70px;
}

h2,h3,p{

    margin:2px 0;
    text-align:center;
}

hr{

    border:none;
    border-top:1px dashed #000;
    margin:6px 0;
}

table{

    width:100%;
    border-collapse:collapse;
}

td{

    padding:2px 0;
}

.total{

    font-size:15px;
    font-weight:bold;
}

.right{

    text-align:right;
}

.center{

    text-align:center;
}

</style>

<script>

window.onload=function(){

    window.print();

    window.onafterprint=function(){

        window.close();

    }

}

</script>

</head>

<body>

<div class="logo">

    <img src="{{ asset('images/logo.png') }}">

</div>

<h2>Farmacia Galo</h2>

<p>León, Nicaragua</p>

<p>Tel: _________</p>

<hr>

<table>

<tr>
<td><strong>Ticket:</strong></td>
<td class="right">{{ $venta->numero_ticket }}</td>
</tr>

<tr>
<td><strong>Fecha:</strong></td>
<td class="right">
{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y H:i') }}
</td>
</tr>

<tr>
<td><strong>Cliente:</strong></td>
<td class="right">
{{ $venta->cliente_nombre ?: 'Consumidor Final' }}
</td>
</tr>

<tr>
<td><strong>Cajero:</strong></td>
<td class="right">{{ $venta->nombre_completo }}</td>
</tr>

<tr>
<td><strong>Pago:</strong></td>
<td class="right">{{ $venta->metodo_pago }}</td>
</tr>

</table>

<hr>

@foreach($detalle as $item)

<b>{{ $item->nombre }}</b>

<table>

<tr>

<td>

{{ $item->cantidad }}
x
C$ {{ number_format($item->precio_unitario,2) }}

</td>

<td class="right">

C$ {{ number_format($item->subtotal,2) }}

</td>

</tr>

</table>

@endforeach

<hr>

<table>

<tr>

<td class="total">

TOTAL

</td>

<td class="right total">

C$ {{ number_format($venta->total,2) }}

</td>

</tr>

@if($venta->metodo_pago=="EFECTIVO" || $venta->metodo_pago=="DOLARES")

<tr>

<td>Recibido</td>

<td class="right">

C$ {{ number_format($venta->monto_pagado,2) }}

</td>

</tr>

<tr>

<td>Cambio</td>

<td class="right">

C$ {{ number_format($venta->cambio,2) }}

</td>

</tr>

@endif

</table>

<hr>

<p>¡Gracias por su compra!</p>

<p>Farmacia Galo</p>

<p>Vuelva pronto</p>

</body>

</html>