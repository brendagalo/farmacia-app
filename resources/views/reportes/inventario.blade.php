@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <h2 class="mb-4">
        📦 Reporte de Inventario
    </h2>

    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card shadow">
                <div class="card-body">
                    <h6>Total de productos</h6>
                    <h3>{{ $totalProductos }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow">
                <div class="card-body">
                    <h6>Costo del Inventario</h6>
                    <h3 class="text-success">
                        C$ {{ number_format($valorInventario,2) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow">
                <div class="card-body">
                    <h6>Stock Bajo</h6>
                    <h3 class="text-warning">
                        {{ $stockBajo }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow">
                <div class="card-body">
                    <h6>Agotados</h6>
                    <h3 class="text-danger">
                        {{ $agotados }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    <div class="card shadow">

        <div class="card-header">
            Productos
        </div>

        <div class="card-body table-responsive">

            <table class="table table-hover">

                <thead class="table-dark">

                    <tr>

                        <th>Código</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Proveedor</th>
                        <th>Stock</th>
                        <th>Mínimo</th>
                        <th>Compra</th>
                        <th>Venta</th>
                        <th>Valor Stock</th>

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

                                <span class="badge bg-danger">
                                    {{ $producto->stock_actual }}
                                </span>

                            @elseif($producto->stock_actual <= $producto->stock_minimo)

                                <span class="badge bg-warning text-dark">
                                    {{ $producto->stock_actual }}
                                </span>

                            @else

                                <span class="badge bg-success">
                                    {{ $producto->stock_actual }}
                                </span>

                            @endif

                        </td>

                        <td>{{ $producto->stock_minimo }}</td>

                        <td>C$ {{ number_format($producto->precio_compra,2) }}</td>

                        <td>C$ {{ number_format($producto->precio_venta,2) }}</td>

                        <td>
                            C$
                            {{ number_format($producto->stock_actual * $producto->precio_compra,2) }}
                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>
<hr>

<div class="d-flex flex-wrap gap-2 mb-4">

    <a href="{{ route('reportes.index') }}"
        class="btn btn-primary">

        <i class="bi bi-arrow-left"></i>
        Regresar
    </a>
    
    <a href="{{ route('reportes.inventario.excel') }}"
        class="btn btn-success">

        <i class="bi bi-file-earmark-excel"></i>
        Exportar Excel
    </a>

    <a href="{{ route('reportes.inventario.pdf') }}"
        class="btn btn-danger">

        <i class="bi bi-file-earmark-pdf"></i>
        Exportar PDF
    </a>

    <a href="{{ route('reportes.inventario.imprimir') }}"
        target="_blank"
        class="btn btn-secondary">

        <i class="bi bi-printer"></i>
        Imprimir

    </a>
    
</div>
@endsection