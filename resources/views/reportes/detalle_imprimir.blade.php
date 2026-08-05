<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">

<title>Voucher</title>

<style>

@media print{

    @page{
        size:80mm auto;
        margin:5mm;
    }

    body{
        width:72mm;
        margin:0 auto;
    }

}

body{

    font-family: Consolas, monospace;
    width:72mm;
    margin:auto;
    font-size:12px;
    color:#000;

}

.logo{

    text-align:center;
    margin-bottom:5px;

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

.info{

    text-align:left;
    font-size:11px;

}

table{

    width:100%;
    border-collapse:collapse;
    font-size:11px;

}

th{

    text-align:left;
    border-bottom:1px dashed #000;
    padding-bottom:3px;

}

td{

    padding:2px 0;

}

.total{

    text-align:right;
    font-size:14px;
    font-weight:bold;
    margin-top:8px;

}

.gracias{

    text-align:center;
    margin-top:12px;
    font-size:11px;

}

</style>

<script>

window.onload=function(){

    window.print();

}

</script>

</head>

<body>

    <div class="logo">

        <img src="{{ asset('images/logo.png') }}">

    </div>

    <h2>Farmacia Galo</h2>

    <p>Tel: 0000-0000</p>

    <p>RUC: 2811704021000B</p>

        <hr>

        <div class="info">

            <strong>Ticket:</strong> {{ $venta->numero_ticket }}<br>

            <strong>Fecha:</strong>
            {{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y H:i') }}
            <br>

            <strong>Cliente:</strong>
            {{ $venta->cliente_nombre ?: 'Consumidor Final' }}
            <br>

            <strong>Cajero:</strong>
            {{ $venta->nombre_completo }}
            <br>

            <strong>Pago:</strong>
            {{ $venta->metodo_pago }}

        </div>

        <hr>

        <table>

            <tr>

                <th>Producto</th>
                <th>Cant</th>
                <th>Total</th>

            </tr>

            @foreach($detalle as $item)

                <tr>

                    <td>{{ $item->nombre }}</td>

                    <td align="center">

                        {{ $item->cantidad }}

                    </td>

                    <td align="right">

                     {{ number_format($item->subtotal,2) }}

                    </td>

                </tr>

            @endforeach

        </table>

        <hr>

        <div class="total">

            TOTAL

            C$ {{ number_format($venta->total,2) }}

        </div>

    @if($venta->metodo_pago=='EFECTIVO')

        <p>

            Pagó:

            C$ {{ number_format($venta->monto_pagado,2) }}

        </p>

        <p>

            Cambio:

            C$ {{ number_format($venta->cambio,2) }}

        </p>

    @endif

    <hr>

    <div class="gracias">

        ¡Gracias por su compra!<br>

        Conserve este comprobante.

    </div>

    </body>

</html>