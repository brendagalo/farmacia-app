@extends('layouts.app')

@section('content')

<div class="container">

    <h2 class="mb-4">
        📊 Módulo de Reportes
    </h2>

    <span class="text-muted">
        {{ now()->format('d/m/Y H:i') }}
    </span>
    
    <div class="row">
        <h2></h2>
        <h2></h2>
        <div class="col-md-3 mb-3">

            <div class="card shadow">

                <div class="card-body text-center">

                    <h5>Ventas</h5>

                    <p>
                        Reportes de ventas.
                    </p>

                    <a href="{{ route('reportes.ventas') }}" class="btn btn-primary">

                        Abrir

                    </a>

                </div>

            </div>

        </div>

        <div class="col-md-3 mb-3">

            <div class="card shadow">

                <div class="card-body text-center">

                    <h5>Inventario</h5>

                    <p>
                        Reporte de productos.
                    </p>

                    <a href="#"
                    class="btn btn-success">

                        Abrir

                    </a>

                </div>

            </div>

        </div>

        <div class="col-md-3 mb-3">

            <div class="card shadow">

                <div class="card-body text-center">

                    <h5>Caja</h5>

                    <p>
                        Arqueos y movimientos.
                    </p>

                    <a href="#"
                    class="btn btn-warning">

                        Abrir

                    </a>

                </div>

            </div>

        </div>

        <div class="col-md-3 mb-3">

            <div class="card shadow">

                <div class="card-body text-center">

                    <h5>Auditoría</h5>

                    <p>
                        Historial del sistema.
                    </p>

                    <a href="#"
                    class="btn btn-danger">

                        Abrir

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection