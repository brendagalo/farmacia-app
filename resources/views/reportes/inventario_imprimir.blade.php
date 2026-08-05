<!DOCTYPE html>
<html lang="es">
    <script>

        window.onload=function(){

            window.print();

            window.onafterprint=function(){

                window.close();

            }

        }

    </script>
    <head>

        <meta charset="UTF-8">

        <title>Reporte de Inventario</title>

        <style>

            body{
                font-family: DejaVu Sans;
                font-size:11px;
                margin:25px;
                color:#333;
            }

            .header{
                text-align:center;
                border-bottom:2px solid #0d6efd;
                padding-bottom:10px;
                margin-bottom:20px;
            }

            .header img{
                width:90px;
            }

            .header h2{
                margin:5px 0;
                color:#0d6efd;
            }

            .header p{
                margin:2px;
                font-size:10px;
            }

            .resumen{
                width:100%;
                margin-bottom:20px;
            }

            .resumen td{
                border:1px solid #ddd;
                padding:8px;
                text-align:center;
            }

            .resumen th{
                background:#0d6efd;
                color:white;
                padding:8px;
            }

            table{
                width:100%;
                border-collapse:collapse;
            }

            th{
                background:#0d6efd;
                color:white;
                padding:7px;
                font-size:10px;
            }

            td{
                border:1px solid #ddd;
                padding:6px;
                font-size:10px;
            }

            tr:nth-child(even){
                background:#f8f8f8;
            }

            .stock-ok{
                color:#198754;
                font-weight:bold;
            }

            .stock-bajo{
                color:#ff9800;
                font-weight:bold;
            }

            .stock-agotado{
                color:#dc3545;
                font-weight:bold;
            }

            .footer{

                margin-top:30px;
                border-top:1px solid #ccc;
                padding-top:10px;
                font-size:10px;
                text-align:center;
                color:#666;

            }

        </style>

    </head>

    <body>

        <div class="header">

            <img src="{{ asset('images/logo.png') }}" width="90">

            <h2>Farmacia Galo</h2>

            <p>Sistema de Gestión Farmacéutica</p>

            <p>Reporte General de Inventario</p>

            <p>
                Generado:
                {{ now()->format('d/m/Y H:i') }}
            </p>

        </div>

        <table class="resumen">

            <tr>

                <th>Total Productos</th>

                <th>Valor Inventario</th>

                <th>Stock Bajo</th>

                <th>Agotados</th>

            </tr>

            <tr>

                <td>{{ $totalProductos }}</td>

                <td>
                    C$
                    {{ number_format($valorInventario,2) }}
                </td>

                <td>{{ $stockBajo }}</td>

                <td>{{ $agotados }}</td>

            </tr>

        </table>

        <h3>Detalle del Inventario</h3>

        <table>

            <thead>

                <tr>

                    <th>Código</th>

                    <th>Producto</th>

                    <th>Categoría</th>

                    <th>Proveedor</th>

                    <th>Stock</th>

                    <th>P. Compra</th>

                    <th>P. Venta</th>

                    <th>Valor</th>

                </tr>

            </thead>

            <tbody>

                @foreach($productos as $producto)

                    <tr>

                        <td>{{ $producto->codigo_barra }}</td>

                        <td>{{ $producto->nombre }}</td>

                        <td>{{ $producto->categoria }}</td>

                        <td>{{ $producto->proveedor }}</td>

                        <td>

                            @if($producto->stock_actual==0)

                                <span class="stock-agotado">
                                AGOTADO
                                </span>

                            @elseif($producto->stock_actual <= $producto->stock_minimo)

                                <span class="stock-bajo">
                                {{ $producto->stock_actual }}
                                </span>

                            @else

                                <span class="stock-ok">
                                {{ $producto->stock_actual }}
                                </span>

                            @endif

                        </td>

                        <td>
                            C$ {{ number_format($producto->precio_compra,2) }}
                        </td>

                        <td>
                            C$ {{ number_format($producto->precio_venta,2) }}
                        </td>

                        <td>
                            C$
                            {{ number_format($producto->stock_actual * $producto->precio_compra,2) }}
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

        <br>

        <table style="width:35%; float:right;">

            <tr>

                <th>Valor Total Inventario</th>

            </tr>

            <tr>

                <td style="text-align:center;font-size:15px;font-weight:bold;">

                    C$
                    {{ number_format($valorInventario,2) }}

                </td>

            </tr>

        </table>

        <div style="clear:both;"></div>

        <div class="footer">

            Documento generado automáticamente por el Sistema de Gestión Farmacia Galo.

            <br>

            Usuario:
            {{ auth()->user()->nombre_completo }}

            <br>

            Fecha:
            {{ now()->format('d/m/Y H:i:s') }}

        </div>

    </body>
</html>