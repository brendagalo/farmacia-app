@extends('layouts.app')

@section('content')

<div class="container-fluid mt-4">

    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Dashboard Farmacia</h2>
        </div>
    </div>

    <div class="row g-4">

        <!-- Ventas Hoy -->
        <div class="col-md-4">
            <div class="card bg-success text-white shadow h-100">
                <div class="card-body d-flex flex-column justify-content-center text-center"
                     style="min-height:150px;">
                    <h5 class="card-title">Ventas Hoy</h5>
                    <h2 class="fw-bold">S/ {{ $ventasHoy }}</h2>
                </div>
            </div>
        </div>

        <!-- Total Productos -->
        <div class="col-md-4">
            <div class="card bg-info text-white shadow h-100">
                <div class="card-body d-flex flex-column justify-content-center text-center"
                     style="min-height:150px;">
                    <h5 class="card-title">Total Productos</h5>
                    <h2 class="fw-bold">{{ $totalProductos }}</h2>
                </div>
            </div>
        </div>

        <!-- Stock Bajo -->
        <div class="col-md-4">
            <div class="card bg-danger text-white shadow h-100">
                <div class="card-body d-flex flex-column justify-content-center text-center"
                     style="min-height:150px;">
                    <h5 class="card-title">Stock Bajo</h5>
                    <h2 class="fw-bold">{{ $stockBajo }}</h2>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
