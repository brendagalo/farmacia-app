<form action="{{ route('usuarios.store') }}" method="POST">
    @csrf

    <label>Usuario</label>
    <input type="text" name="username">

    <label>Contraseña</label>
    <input type="password" name="password">

    <label>Rol</label>
    <select name="id_rol">
        @foreach($roles as $rol)
            <option value="{{ $rol->id_rol }}">
                {{ $rol->nombre }}
            </option>
        @endforeach
    </select>

    <button type="submit">Guardar</button>
</form>