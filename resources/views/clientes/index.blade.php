<h2>Clientes</h2>

<a href="{{ route('clientes.create') }}">
    Nuevo Cliente
</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Cédula</th>
            <th>Teléfono</th>
            <th>Acciones</th>
        </tr>
    </thead>

    <tbody>
        @foreach($clientes as $cliente)
        <tr>
            <td>{{ $cliente->id_cliente }}</td>
            <td>{{ $cliente->nombres }} {{ $cliente->apellidos }}</td>
            <td>{{ $cliente->cedula }}</td>
            <td>{{ $cliente->telefono }}</td>

            <td>
                <a href="{{ route('clientes.edit',$cliente->id_cliente) }}">
                    Editar
                </a>

                <form action="{{ route('clientes.destroy',$cliente->id_cliente) }}"
                      method="POST">
                    @csrf
                    @method('DELETE')

                    <button type="submit">
                        Eliminar
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>