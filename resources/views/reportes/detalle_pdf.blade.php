<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Venta</title>

    <style>

        body{
            font-family: DejaVu Sans;
            font-size:12px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }

        th{
            background:#0d6efd;
            color:white;
            padding:8px;
        }

        td{
            border:1px solid #ccc;
            padding:6px;
        }

        h2,h4{
            margin:0;
        }

    </style>

</head>
<body>

<center>
    <img src="{{ public_path('images/logo.png') }}"
        width="120"
        alt="Logo">
    <h2>Farmacia Galo</h2>

    <h4>Detalle de Venta</h4>

</center>

<hr>

<p><strong>Ticket:</strong> {{ $venta->numero_ticket }}</p>

<p><strong>Fecha:</strong> {{ $venta->fecha_venta }}</p>

<p><strong>Cliente:</strong> {{ $venta->cliente_nombre }}</p>

<p><strong>Usuario:</strong> {{ $venta->nombre_completo }}</p>

<table>

    <thead>

        <tr>

            <th>Producto</th>

            <th>Cantidad</th>

            <th>Precio</th>

            <th>Subtotal</th>

        </tr>

    </thead>

    <tbody>

        @foreach($detalle as $item)

        <tr>

            <td>{{ $item->nombre }}</td>

            <td>{{ $item->cantidad }}</td>

            <td>C$ {{ number_format($item->precio_unitario,2) }}</td>

            <td>C$ {{ number_format($item->subtotal,2) }}</td>

        </tr>

        @endforeach

    </tbody>

</table>

<h3 style="text-align:right">

    Total:
    C$ {{ number_format($venta->total,2) }}

</h3>

</body>
</html>