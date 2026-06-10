@extends('layouts.app')

@section('content')

<div class="container">

    <h3>Editar Producto</h3>

    <form action="{{ route('productos.update', $producto->id_producto) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nombre</label>
            <input type="text"
                   name="nombre"
                   class="form-control"
                   value="{{ $producto->nombre }}">
        </div>

        <div class="mb-3">
            <label>Descripción</label>
            <input type="text"
                   name="descripcion"
                   class="form-control"
                   value="{{ $producto->descripcion }}">
        </div>

        <div class="mb-3">
            <label>Precio</label>
            <input type="number"
                   step="0.01"
                   name="precio_venta"
                   class="form-control"
                   value="{{ $producto->precio_venta }}">
        </div>

        <div class="mb-3">
            <label>Stock</label>
            <input type="number"
                   name="stock_actual"
                   class="form-control"
                   value="{{ $producto->stock_actual }}">
        </div>

        <button class="btn btn-success">
            Actualizar
        </button>

        <a href="{{ route('productos.index') }}"
           class="btn btn-secondary">
            Cancelar
        </a>

    </form>

</div>

@endsection