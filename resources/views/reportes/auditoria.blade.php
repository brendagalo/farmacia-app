@extends('layouts.app')

@section('content')

<div class="container-fluid p-4">

    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold text-primary">
                <i class="bi bi-shield-check"></i>
                Reporte de Auditoría
            </h2>

            <small class="text-muted">
                {{ now()->format('d/m/Y H:i') }}
            </small>
        </div>

    </div>

    {{-- Tarjetas --}}
    <div class="row g-3 mb-4">

        <div class="col-md-3">

            <div class="card shadow border-0">

                <div class="card-body">

                    <h6 class="text-muted">Total registros</h6>

                    <h3 class="text-primary">
                        {{ $totalRegistros }}
                    </h3>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card shadow border-0">

                <div class="card-body">

                    <h6 class="text-muted">INSERT</h6>

                    <h3 class="text-success">
                        {{ $insertados }}
                    </h3>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card shadow border-0">

                <div class="card-body">

                    <h6 class="text-muted">UPDATE</h6>

                    <h3 class="text-warning">
                        {{ $actualizados }}
                    </h3>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card shadow border-0">

                <div class="card-body">

                    <h6 class="text-muted">DELETE</h6>

                    <h3 class="text-danger">
                        {{ $eliminados }}
                    </h3>

                </div>

            </div>

        </div>

    </div>

    {{-- Filtros --}}
    <div class="card shadow border-0 mb-4">

        <div class="card-header bg-light">

            <strong>
                🔎 Filtros
            </strong>

        </div>

        <div class="card-body">

            <form method="GET">

                <div class="row g-3">

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

                        <label>Usuario</label>

                        <select name="usuario" class="form-select">

                            <option value="">Todos</option>

                            @foreach($usuarios as $u)

                                <option value="{{ $u->id_usuario }}"
                                    {{ request('usuario')==$u->id_usuario ? 'selected':'' }}>

                                    {{ $u->nombre_completo }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-2">

                        <label>Tabla</label>

                        <input type="text"
                               name="tabla"
                               class="form-control"
                               value="{{ request('tabla') }}">

                    </div>

                    <div class="col-md-2">

                        <label>Acción</label>

                        <select name="accion" class="form-select">

                            <option value="">Todas</option>

                            <option value="INSERT" {{ request('accion')=='INSERT' ? 'selected':'' }}>
                                INSERT
                            </option>

                            <option value="UPDATE" {{ request('accion')=='UPDATE' ? 'selected':'' }}>
                                UPDATE
                            </option>

                            <option value="DELETE" {{ request('accion')=='DELETE' ? 'selected':'' }}>
                                DELETE
                            </option>

                        </select>

                    </div>

                    <div class="col-md-2 d-flex align-items-end">

                        <button class="btn btn-primary me-2">
                            <i class="bi bi-search"></i>
                            Buscar
                        </button>

                        <a href="{{ route('reportes.auditoria') }}"
                           class="btn btn-secondary">

                            Limpiar

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>

    {{-- Botones --}}
    <div class="mb-3 d-flex gap-2 flex-wrap">

        <a href="{{ route('reportes.index') }}"
           class="btn btn-primary">

            <i class="bi bi-arrow-left"></i>
            Regresar

        </a>

        <a href="{{ route('reportes.auditoria.excel', request()->all()) }}"
           class="btn btn-success">

            <i class="bi bi-file-earmark-excel"></i>
            Exportar Excel

        </a>

        <a href="{{ route('reportes.auditoria.pdf', request()->all()) }}"
           class="btn btn-danger">

            <i class="bi bi-file-earmark-pdf"></i>
            Exportar PDF

        </a>
{{--
        <a href="{{ route('reportes.auditoria.imprimir', request()->all()) }}"
           target="_blank"
           class="btn btn-secondary">

            <i class="bi bi-printer"></i>
            Imprimir

        </a>
--}}
    </div>

    {{-- Tabla --}}
    <div class="card shadow border-0">

        <div class="card-header bg-light">

            <strong>
                📋 Historial de Auditoría
            </strong>

        </div>

        <div class="card-body table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-dark">

                    <tr>

                        <th>#</th>

                        <th>Fecha</th>

                        <th>Usuario</th>

                        <th>Tabla</th>

                        <th>Acción</th>

                        <th>Registro</th>

                        <th>IP</th>

                        <th>Detalles</th>

                    </tr>

                </thead>

                <tbody>

                @forelse($auditorias as $a)

                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        <td>
                            {{ \Carbon\Carbon::parse($a->creado_en)->format('d/m/Y H:i') }}
                        </td>

                        <td>
                            {{ $a->nombre_completo }}
                        </td>

                        <td>

                            <span class="badge bg-info">
                                {{ $a->tabla_afectada }}
                            </span>

                        </td>

                        <td>

                            @if($a->accion=="INSERT")

                                <span class="badge bg-success">

                                    INSERT

                                </span>

                            @elseif($a->accion=="UPDATE")

                                <span class="badge bg-warning text-dark">

                                    UPDATE

                                </span>

                            @else

                                <span class="badge bg-danger">

                                    DELETE

                                </span>

                            @endif

                        </td>

                        <td>
                            {{ $a->registro_id }}
                        </td>

                        <td>
                            {{ $a->ip_address }}
                        </td>

                        <td>

                            <button class="btn btn-outline-primary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal{{ $a->id_auditoria }}">

                                <i class="bi bi-eye"></i>

                            </button>

                        </td>

                    </tr>

                    {{-- Modal --}}

                    <div class="modal fade"
                         id="modal{{ $a->id_auditoria }}">

                        <div class="modal-dialog modal-lg">

                            <div class="modal-content">

                                <div class="modal-header">

                                    <h5>Detalle Auditoría</h5>

                                    <button class="btn-close"
                                            data-bs-dismiss="modal">

                                    </button>

                                </div>

                                <div class="modal-body">

                                    <h6>Datos anteriores</h6>

                                    <pre>{{ json_encode(json_decode($a->datos_anteriores), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>

                                    <hr>

                                    <h6>Datos nuevos</h6>

                                    <pre>{{ json_encode(json_decode($a->datos_nuevos), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>

                                </div>

                            </div>

                        </div>

                    </div>

                @empty

                    <tr>

                        <td colspan="8"
                            class="text-center text-muted">

                            No existen registros.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection