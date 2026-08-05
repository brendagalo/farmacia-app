@extends('layouts.app')

@section('content')

    <div class="container-fluid p-4">

        {{-- Encabezado --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-primary mb-0">
                    <i class="bi bi-cash-stack"></i>
                    Reporte de Caja
                </h2>
                <small class="text-muted">
                    {{ now()->format('d/m/Y H:i') }}
                </small>
            </div>
        </div>

        {{-- Tarjetas resumen --}}
        <div class="row g-3 mb-4">

            <div class="col-md-3">
                <div class="card shadow border-0 h-100">
                    <div class="card-body">
                        <h6 class="text-muted">Total ingresos</h6>
                        <h3 class="text-success mb-0">
                            C$ {{ number_format($totalIngresos,2) }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow border-0 h-100">
                    <div class="card-body">
                        <h6 class="text-muted">Total egresos</h6>
                        <h3 class="text-danger mb-0">
                            C$ {{ number_format($totalEgresos,2) }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow border-0 h-100">
                    <div class="card-body">
                        <h6 class="text-muted">Movimientos</h6>
                        <h3 class="text-primary mb-0">
                            {{ $movimientos->count() }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow border-0 h-100">
                    <div class="card-body">
                        <h6 class="text-muted">Arqueos registrados</h6>
                        <h3 class="text-warning mb-0">
                            {{ $cajas->count() }}
                        </h3>
                    </div>
                </div>
            </div>

        </div>

        <div class="card shadow border-0 mb-4">

            <div class="card-header bg-light">
                <strong>🔎 Filtros</strong>
            </div>

            <div class="card-body">

                <form method="GET" action="{{ route('reportes.caja') }}">

                    <div class="row">

                        <div class="col-md-3">
                            <label>Fecha</label>

                            <input
                                type="date"
                                name="fecha"
                                class="form-control"
                                value="{{ request('fecha') }}">
                        </div>

                        <div class="col-md-3">
                            <label>Estado de Caja</label>

                            <select name="estado" class="form-select">

                                <option value="">Todas</option>

                                <option value="ABIERTA"
                                    {{ request('estado')=='ABIERTA' ? 'selected':'' }}>
                                    ABIERTA
                                </option>

                                <option value="CERRADA"
                                    {{ request('estado')=='CERRADA' ? 'selected':'' }}>
                                    CERRADA
                                </option>

                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">

                            <button class="btn btn-primary me-2">
                                🔎 Buscar
                            </button>

                            <a href="{{ route('reportes.caja') }}"
                            class="btn btn-secondary">
                                Limpiar
                            </a>

                        </div>

                    </div>

                    </form>

                </div>

            </div>

            {{-- Métodos de pago --}}
        <div class="card shadow border-0 mb-4">

            <div class="card-header bg-light">
                <strong>Ventas por método de pago</strong>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-3">
                        <div class="alert alert-success mb-0">
                            <div>💵 Efectivo</div>
                            <h5 class="mb-0">
                                C$ {{ number_format($ventasEfectivo,2) }}
                            </h5>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="alert alert-primary mb-0">
                            <div>💳 Tarjeta</div>
                            <h5 class="mb-0">
                                C$ {{ number_format($ventasTarjeta,2) }}
                            </h5>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="alert alert-warning mb-0">
                            <div>🏦 Transferencia</div>
                            <h5 class="mb-0">
                                C$ {{ number_format($ventasTransferencia,2) }}
                            </h5>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="alert alert-info mb-0">
                            <div>💲 Dólares</div>
                            <h5 class="mb-0">
                                C$ {{ number_format($ventasDolares,2) }}
                            </h5>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="card shadow border-0 mb-4">

        <div class="card-header bg-light">
            <h5 class="mb-0">
                📦 Historial de Arqueos de Caja
            </h5>
        </div>

        <div class="card-body table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-dark">

                    <tr>

                        <th>#</th>
                        <th>Apertura</th>
                        <th>Cierre</th>
                        <th>Estado</th>
                        <th>Saldo Inicial</th>
                        <th>Saldo Final</th>
                        <th>Tipo Cambio</th>

                    </tr>

                </thead>

                <tbody>

                @forelse($cajas as $caja)

                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        <td>

                            {{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m/Y H:i') }}

                        </td>

                        <td>

                            @if($caja->fecha_cierre)

                                {{ \Carbon\Carbon::parse($caja->fecha_cierre)->format('d/m/Y H:i') }}

                            @else

                                —

                            @endif

                        </td>

                        <td>

                            @if($caja->estado=="ABIERTA")

                                <span class="badge bg-success">

                                    ABIERTA

                                </span>

                            @else

                                <span class="badge bg-danger">

                                    CERRADA

                                </span>

                            @endif

                        </td>

                        <td>

                            C$ {{ number_format($caja->saldo_inicial,2) }}

                        </td>

                        <td>

                            C$ {{ number_format($caja->saldo_final,2) }}

                        </td>

                        <td>

                            {{ number_format($caja->tipo_cambio,2) }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7" class="text-center">

                            No existen arqueos registrados.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <div class="card shadow border-0">

        <div class="card-header bg-light">

            <h5 class="mb-0">
                💰 Historial de Movimientos de Caja
            </h5>

        </div>

        <div class="card-body table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-dark">

                    <tr>

                        <th>#</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Forma Pago</th>
                        <th>Monto</th>
                        <th>Saldo Anterior</th>
                        <th>Saldo Actual</th>
                        <th>Usuario</th>
                        <th>Descripción</th>

                    </tr>

                </thead>

                <tbody>

                @forelse($movimientos as $mov)

                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        <td>
                            {{ \Carbon\Carbon::parse($mov->creado_en)->format('d/m/Y H:i') }}
                        </td>

                        <td>

                            @if($mov->tipo_movimiento=="INGRESO")

                                <span class="badge bg-success">

                                    INGRESO

                                </span>

                            @else

                                <span class="badge bg-danger">

                                    EGRESO

                                </span>

                            @endif

                        </td>

                        <td>

                            @switch($mov->forma_pago)

                                @case('EFECTIVO')
                                    💵 Efectivo
                                    @break

                                @case('TARJETA')
                                    💳 Tarjeta
                                    @break

                                @case('TRANSFERENCIA')
                                    🏦 Transferencia
                                    @break

                                @case('DOLARES')
                                    💲 Dólares
                                    @break

                                @default
                                    {{ $mov->forma_pago }}

                            @endswitch

                        </td>

                        <td>

                            <strong>
                                C$ {{ number_format($mov->monto,2) }}
                            </strong>

                        </td>

                        <td>

                            C$ {{ number_format($mov->saldo_anterior,2) }}

                        </td>

                        <td>

                            C$ {{ number_format($mov->saldo_actual,2) }}

                        </td>

                        <td>

                            {{ $mov->nombre_completo }}

                        </td>

                        <td>

                            {{ $mov->descripcion }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="9" class="text-center text-muted">

                            No existen movimientos registrados.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endsection