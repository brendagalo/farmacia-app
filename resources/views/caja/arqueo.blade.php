@extends('layouts.app')

@section('content')

<div class="container">

    <div class="card shadow">

        <div class="card-header bg-warning">

            Arqueo de Caja

        </div>

        <div class="card-body">

            <form action="{{ route('caja.arqueo.guardar') }}"
                  method="POST">

                @csrf

                <div class="mb-3">

                    <label>Saldo esperado</label>

                    <input
                        class="form-control"
                        value="{{ number_format($caja->saldo_final,2) }}"
                        readonly>

                </div>

                <div class="mb-3">

                    <label>Efectivo contado</label>

                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="saldo_contado"
                        class="form-control"
                        required>

                </div>

                <button class="btn btn-warning">

                    Calcular Diferencia

                </button>
                <button class="btn btn-secondary"
                    onclick="window.history.back()">

                    Regresar

                </button>

            </form>

        </div>

    </div>

</div>

@endsection