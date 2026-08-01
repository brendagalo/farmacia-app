<!DOCTYPE html>
<html>

    <head>

        <meta charset="UTF-8">

        <title>Reporte de Ventas</title>

        <style>

            body{

                font-family: DejaVu Sans;

                font-size:12px;

            }

            table{

                width:100%;

                border-collapse:collapse;

                margin-top:15px;

            }

            th{

                background:#0d6efd;

                color:white;

                padding:8px;

                font-size:11px;

            }

            td{

                padding:6px;

                border:1px solid #ccc;

                font-size:10px;

            }

            tr:nth-child(even){

                background:#f5f5f5;

            }

        </style>

    </head>

    <body>

        <h2 align="center">

         Reporte de Ventas

        </h2>

        <div style="text-align:center; margin-bottom:20px;">

            <h1 style="margin:0; color:#1f4e79;">
                FARMACIA Galo
            </h1>

            <h2 style="margin:5px 0;">
                Reporte General de Ventas
            </h2>

            <p>
                Fecha de generación:
                {{ now()->format('d/m/Y H:i') }}
            </p>

        </div>

        <table>

            <thead>

                <tr>

                    <th>Ticket</th>

                    <th>Fecha</th>

                    <th>Cliente</th>

                    <th>Usuario</th>

                    <th>Total</th>

                    <th>Estado</th>

                </tr>

            </thead>

            <tbody>

                @foreach($ventas as $venta)

                    <tr>

                        <td>{{ $venta->numero_ticket }}</td>

                        <td>{{ $venta->fecha_venta }}</td>

                        <td>{{ $venta->cliente_nombre }}</td>

                        <td>{{ $venta->nombre_completo }}</td>

                        <td>C$ {{ number_format($venta->total,2) }}</td>

                        <td>{{ $venta->estado }}</td>

                    </tr>

                @endforeach

            </tbody>

        </table>

        <br><br>

        <table style="width:40%; float:right;">

            <tr>

                <th>Total de ventas</th>

                <td>

                    {{ $ventas->where('estado','COMPLETADA')->count() }}
                </td>

            </tr>

            <tr>

                <th>Monto vendido</th>

                <td>

                    C$ {{ number_format($ventas->where('estado','COMPLETADA')->sum('total'), 2) }}

                </td>

            </tr>

        </table>

    </body>

</html>