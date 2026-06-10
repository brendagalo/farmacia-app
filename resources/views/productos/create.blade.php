@extends('layouts.app')

@section('content')

<div class="container">

    <h3>Nuevo Producto</h3>

    <form action="{{ route('productos.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Descripción</label>
            <input type="text" name="descripcion" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Precio</label>
            <input type="number" step="0.01" name="precio_venta" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Stock</label>
            <input type="number" name="stock_actual" class="form-control" required>
        </div>

        <button class="btn btn-success">Guardar</button>
        <button class="btn btn-secondary" type="button" onclick="window.history.back()">Cancelar</button>

    </form>

</div>

@endsection
