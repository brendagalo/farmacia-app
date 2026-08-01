@extends('layouts.app')

@section('content')

<div class="container">
        <img
            src="{{ asset('images/logo.png') }}"
            width="240"
            alt="Logo">

        <h2 class="mb-4">

        📋 Maestro - Detalle de Venta

        </h2>

    <div class="card shadow">

        <div class="card-body">

                <h4>

                    {{ $venta->numero_ticket }}

                </h4>

                <hr>

                <div class="row">

                    <div class="col-md-4">

                        <strong>Cliente</strong>

                        <br>

                        {{ $venta->cliente_nombre ?: 'Consumidor Final' }}

                    </div>

                    <div class="col-md-4">

                        <strong>Usuario</strong>

                        <br>

                        {{ $venta->nombre_completo }}

                    </div>

                    <div class="col-md-4">

                            <strong>Fecha</strong>

                            <br>

                            {{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y H:i') }}

                    </div>

                </div>

            <hr>

            <table class="table table-bordered">

                    <thead class="table-primary">

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

                <tfoot>

                    <tr>

                        <th colspan="3" class="text-end">

                            TOTAL

                        </th>

                        <th>

                            C$ {{ number_format($venta->total,2) }}

                        </th>

                    </tr>

                </tfoot>

            </table>

            <a
                href="{{ route('reportes.ventas') }}"
                class="btn btn-secondary">

                ← Volver

            </a>

        </div>

    </div>

</div>

@endsection