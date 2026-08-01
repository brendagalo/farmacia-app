@extends('layouts.app')

@section('content')
    <style>

        .btn-pastel-green{
            background:#CDECCF;
            color:#2E7D32;
            border:1px solid #A5D6A7;
        }

        .btn-pastel-green:hover{
            background:#B7E4BC;
            color:#1B5E20;
        }

        .btn-pastel-red{
            background:#F8D7DA;
            color:#B02A37;
            border:1px solid #F1AEB5;
        }

        .btn-pastel-red:hover{
            background:#F5C2C7;
            color:#842029;
        }

        .btn-pastel-blue{
            background:#D6ECFF;
            color:#0A58CA;
            border:1px solid #9EC5FE;
        }

        .btn-pastel-blue:hover{
            background:#C4E1FF;
            color:#084298;
        }

        .btn-pastel-yellow{
            background:#FFF3CD;
            color:#997404;
            border:1px solid #FFE69C;
        }

        .btn-pastel-yellow:hover{
            background:#FFE8A1;
            color:#7A5C00;
        }

        .btn-pastel-purple{
            background:#E8D9F8;
            color:#6F42C1;
            border:1px solid #D4B8F5;
        }

        .btn-pastel-purple:hover{
            background:#DDC6F5;
            color:#59359A;
        }

        .btn-pastel{
            height:95px;
            font-weight:600;
            border-radius:12px;
            transition:.3s;
        }

        .btn-pastel:hover{
            transform:translateY(-2px);
            box-shadow:0 5px 15px rgba(0,0,0,.15);
        }

    </style>
<div class="container-fluid p-4">

    <h2 class="mb-4 fw-bold text-primary" >
        
        <i class="bi bi-graph-up-arrow"></i>
        Reporte de Ventas
    </h2>

    <span class="text-muted">
            {{ now()->format('d/m/Y H:i') }}
        </span>
    <div class="row">

        <!-- Total vendido -->
        <div class="col-md-3">

            <div class="card shadow border-0">

                <div class="card-body">

                    <h6 class="text-muted">

                        Total vendido

                    </h6>

                    <h3 class="text-success">

                        C$ {{ number_format($ventas->where('estado','COMPLETADA')->sum('total'), 2) }}

                    </h3>

                </div>

            </div>

        </div>

        <!-- Número de ventas -->
        <div class="col-md-3">

            <div class="card shadow border-0">

                <div class="card-body">

                    <h6 class="text-muted">

                        Número de ventas

                    </h6>

                    <h3 class="text-primary">

                        {{ $ventas->count() }}

                    </h3>

                </div>

            </div>

        </div>

        <!-- Ventas anuladas -->
        <div class="col-md-3">

            <div class="card shadow border-0">

                <div class="card-body">

                    <h6 class="text-muted">

                        Ventas anuladas

                    </h6>

                    <h3 class="text-danger">

                        {{ $ventas->where('estado','ANULADA')->count() }}

                    </h3>

                </div>

            </div>

        </div>

        <!-- Promedio -->
        <div class="col-md-3">

            <div class="card shadow border-0">

                <div class="card-body">

                    <h6 class="text-muted">

                        Promedio por venta

                    </h6>

                    <h3 class="text-warning">

                        C$ {{ $ventas->count() ? number_format($ventas->sum('total')/$ventas->count(),2): '0.00' }}

                    </h3>

                </div>

            </div>

        </div>

    </div>

    <div class="card shadow border-0 mt-4">

        <div class="card-header bg-light">

            <h5 class="mb-0">

                🔎 Filtros

            </h5>

        </div>

        <div class="card-body">

            <form method="GET">

                <div class="row">

                    <div class="col-md-2">

                        <label>Desde</label>

                        <input
                            type="date"
                            name="desde"
                            class="form-control"
                            value="{{ request('desde') }}">

                    </div>

                    <div class="col-md-2">

                        <label>Hasta</label>

                        <input
                            type="date"
                            name="hasta"
                            class="form-control"
                            value="{{ request('hasta') }}">

                    </div>

                    <div class="col-md-2">

                        <label>Usuario</label>

                        <select
                            name="usuario"
                            class="form-select">

                            <option value="">Todos</option>

                            @foreach($usuarios as $u)

                            <option
                                value="{{ $u->id_usuario }}"
                                {{ request('usuario')==$u->id_usuario ? 'selected':'' }}>

                                {{ $u->nombre_completo }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-2">

                        <label>Método</label>

                        <select
                            name="metodo"
                            class="form-select">

                            <option value="">Todos</option>

                            <option value="EFECTIVO">EFECTIVO</option>

                            <option value="DOLARES">DÓLARES</option>

                            <option value="TARJETA">TARJETA</option>

                            <option value="TRANSFERENCIA">TRANSFERENCIA</option>

                        </select>

                    </div>

                    <div class="col-md-2">

                        <label>Estado</label>

                        <select
                            name="estado"
                            class="form-select">

                            <option value="">Todos</option>

                            <option value="COMPLETADA">COMPLETADA</option>

                            <option value="ANULADA">ANULADA</option>

                        </select>

                    </div>

                    <div class="col-md-2">

                        <label>Cliente</label>

                        <input
                            type="text"
                            name="cliente"
                            class="form-control"
                            value="{{ request('cliente') }}">

                    </div>

                </div>

                <div class="mt-4">

                    <button class="btn btn-primary">

                        🔎 Buscar

                    </button>

                    <a
                        href="{{ route('reportes.ventas') }}"
                        class="btn btn-secondary">

                        Limpiar

                    </a>

                </div>

            </form>

        </div>

    </div>

    
    <div class="d-flex flex-wrap gap-2 mt-4 mb-3">

        <a href="{{ route('reportes.ventas.excel', request()->all()) }}"
            class="btn btn-success">

            <i class="bi bi-file-earmark-excel"></i>
            Exportar Excel

        </a>

        <a href="{{ route('reportes.ventas.pdf') }}"
            class="btn btn-danger">
            
                <i class="bi bi-file-earmark-pdf"></i>
                Exportar PDF
        </a>

        <a href="{{ route('reportes.ventas.imprimir', request()->all()) }}"
            target="_blank"
            class="btn btn-secondary">

                <i class="bi bi-printer"></i>
                Imprimir
        </a>

        
    </div>


    <div class="card shadow border-0 mt-4">

        <div class="card-header bg-light">

            <h5 class="mb-0">

                📋 Resultado del Reporte

            </h5>

        </div>

        <div class="card-body table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-dark">

                    <tr>

                        <th>#</th>

                        <th>Ticket</th>

                        <th>Fecha</th>

                        <th>Cliente</th>

                        <th>Usuario</th>

                        <th>Método</th>

                        <th>Total</th>

                        <th>Estado</th>

                        <th>Acciones</th>

                    </tr>

                </thead>

                <tbody>

                @forelse($ventas as $venta)

                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        <td>

                            <strong class="text-primary">

                                {{ $venta->numero_ticket }}

                            </strong>

                        </td>

                        <td>

                            {{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y H:i') }}

                        </td>

                        <td>

                            {{ $venta->cliente_nombre ?: 'Consumidor Final' }}

                        </td>

                        <td>

                            {{ $venta->nombre_completo }}

                        </td>

                        <td>

                            @switch($venta->metodo_pago)

                                @case('EFECTIVO')
                                    <span class="badge rounded-pill bg-success">
                                        💵 Efectivo
                                    </span>
                                    @break

                                @case('DOLARES')
                                    <span class="badge rounded-pill bg-info text-dark">
                                        💲 Dólares
                                    </span>
                                    @break

                                @case('TARJETA')
                                    <span class="badge rounded-pill bg-primary">
                                        💳 Tarjeta
                                    </span>
                                    @break

                                @case('TRANSFERENCIA')
                                    <span class="badge rounded-pill bg-warning text-dark">
                                        🏦 Transferencia
                                    </span>
                                    @break

                                @default
                                    {{ $venta->metodo_pago }}

                            @endswitch

                        </td>

                        <td>

                            <strong>

                                C$ {{ number_format($venta->total,2) }}

                            </strong>

                        </td>

                        <td>

                            @if($venta->estado=='COMPLETADA')

                                <span class="badge bg-success">

                                    COMPLETADA

                                </span>

                            @else

                                <span class="badge bg-danger">

                                    ANULADA

                                </span>

                            @endif

                        </td>

                        <td class="text-nowrap">
                            
                            {{-- Ver venta --}}
                            <a href="{{ route('ventas.show',$venta->id_venta) }}"
                            class="btn btn-sm btn-outline-primary"
                            title="Ver venta">

                                <i class="bi bi-eye"></i>

                            </a>
                                
                            {{-- Maestro Detalle --}}
                            <a href="{{ route('reportes.ventas.detalle',$venta->id_venta) }}"
                            class="btn btn-sm btn-outline-success"
                            title="Maestro Detalle">

                                <i class="bi bi-list-ul"></i>

                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="9" class="text-center text-muted">

                            No existen ventas para mostrar.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>
</div>

@endsection