@extends('layouts.app')

@section('content')

<div class="container">

    <h2 class="mb-4">
        Movimientos de Caja
    </h2>


    <form method="GET" action="{{ route('caja.movimientos') }}" class="row mb-4">

        <div class="col-md-2">
            <label>Desde</label>
            <input type="date"
                name="desde"
                class="form-control"
                value="{{ request('desde') }}">
        </div>

        <div class="col-md-2">
            <label>Hasta</label>
            <input type="date"
                name="hasta"
                class="form-control"
                value="{{ request('hasta') }}">
        </div>

        <div class="col-md-2">
            <label>Tipo</label>

            <select name="tipo" class="form-control">

                <option value="">Todos</option>
                <option value="INGRESO">Ingreso</option>
                <option value="EGRESO">Egreso</option>

            </select>

        </div>

        <div class="col-md-2">
            <label>Método</label>

            <select name="metodo" class="form-control">

                <option value="">Todos</option>
                <option value="EFECTIVO">Efectivo</option>
                <option value="TARJETA">Tarjeta</option>
                <option value="TRANSFERENCIA">Transferencia</option>
                <option value="DOLARES">Dólares</option>

            </select>

        </div>

        <div class="col-md-2">
            <label>Cliente</label>
                <input type="text"
                    name="cliente"
                    class="form-control"
                    value="{{ request('cliente') }}">
                    
        </div>

        <div class="col-md-2 d-flex align-items-end">

            <button class="btn btn-primary w-100">
                Buscar
            </button>

        </div>
        <h1></h1>

        <div class="col-md-2 d-flex align-items-end">

            <a href="{{ route('caja.movimientos') }}" class="btn btn-secondary w-100">
                Limpiar
            </a>
        </div>

    </form>
    <div class="card shadow">

        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover">

                <thead class="table-dark">

                    <tr>

                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Ticket</th>
                        <th>Cliente</th>
                        <th>Método de Pago</th>
                        <th>Monto</th>
                        <th>Motivo</th>
                        <th>Descripción</th>
                        <th>Usuario</th>
                        <th>Saldo Anterior</th>
                        <th>Saldo Actual</th>
                    
                    </tr>

                </thead>

                <tbody>

                    @forelse($movimientos as $m)

                    <tr>

                        <td>{{ $m->creado_en }}</td>

                        <td>

                            @if($m->id_venta)

                                <span class="badge bg-primary">

                                    🛒 VENTA

                                </span>

                            @elseif($m->tipo_movimiento=='INGRESO')

                                <span class="badge bg-success">

                                    ➕ INGRESO

                                </span>

                            @else

                                <span class="badge bg-danger">

                                    ➖ EGRESO

                                </span>

                            @endif

                        </td>
                        <td>

                            {{ $m->numero_ticket ?? '-' }}

                        </td>

                        <td>

                            {{ $m->cliente_nombre ?? '-' }}

                        </td>

                        <td>
                            {{ $m->metodo_pago ?? $m->forma_pago }}
                        </td>
                        <td>

                            C$
                            {{ number_format($m->monto,2) }}

                        </td>

                        <td>{{ $m->motivo }}</td>

                        <td>{{ $m->descripcion }}</td>

                        <td>{{ $m->nombre_completo }}</td>

                        <td>

                            C$
                            {{ number_format($m->saldo_anterior,2) }}

                        </td>

                        <td>

                            C$
                            {{ number_format($m->saldo_actual,2) }}

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="11" class="text-center">

                            No existen movimientos registrados.

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>
        
        <button class="btn btn-secondary mt-3"
                onclick="window.history.back()">

            Regresar
        </button>
@endsection