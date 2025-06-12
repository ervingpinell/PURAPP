@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <h1>Gestión de Usuarios</h1>
@stop

@section('content')
        <div class="p-3 table-responsive">
            <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
                <i class="fas fa-plus"></i> Añadir Usuario
            </a>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.users.index') }}">
                        <!-- Filtro por rol -->
                        <div class="row justify-content-center mb-2">
                            <div class="col-md-4 text-center">
                                <label for="rol" class="form-label">Filtrar por rol:</label>
                                <select name="rol" id="rol" class="form-select text-center">
                                    <option value="">-- Todos --</option>
                                    @foreach ($roles as $rol)
                                        <option value="{{ $rol->id_role }}" {{ request('rol') == $rol->id_role ? 'selected' : '' }}>
                                            {{ $rol->role_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- Filtro por estado -->
                        <div class="row justify-content-center mb-2">
                            <div class="col-md-4 text-center">
                                <label for="estado" class="form-label">Filtrar por estado:</label>
                                <select name="estado" id="estado" class="form-select text-center">
                                    <option value="">-- Todos --</option>
                                    <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>Activos</option>
                                    <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivos</option>
                                </select>
                            </div>
                        </div>

                        <!-- Filtro por correo -->
                        <div class="row justify-content-center mb-2">
                            <div class="col-md-4 text-center">
                                <label for="email" class="form-label">Filtrar por correo:</label>
                                <input type="email" name="email" id="email" class="form-control text-center"
                                placeholder="ejemplo@correo.com" value="{{ request('email') }}">
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="row justify-content-center">
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>




    <table class="table table-striped table-bordered table-hover">
        <thead class="bg-primary text-white">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Numero de Telefono</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id_user }}</td>
                    <td>{{ $user->full_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role->role_name ?? 'Sin rol' }}</td>
                    <td>{{$user->phone}}</td>
                    <td>
                        @if ($user->status)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <!-- Botón editar -->
                        <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $user->id_user }}">
                            <i class="fas fa-edit"></i>
                        </a>

                        <!-- Botón desactivar -->
                        <form action="{{ route('admin.users.destroy', $user->id_user) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="btn btn-sm {{ $user->status ? 'btn-danger' : 'btn-success' }}"
                                onclick="return confirm('{{ $user->status ? '¿Deseas desactivar este usuario?' : '¿Deseas reactivar este usuario?' }}')">
                                <i class="fas {{ $user->status ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                            </button>
                        </form>

                    </td>
                </tr>

                <!-- Modal Editar -->
                <div class="modal fade" id="modalEditar{{ $user->id_user }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('admin.users.update', $user->id_user) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Editar Usuario</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label>Nombre</label>
                                        <input type="text" name="full_name" class="form-control" value="{{ $user->full_name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Correo</label>
                                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Rol</label>
                                        <select name="id_role" class="form-control" required>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id_role }}" {{ $user->id_role == $role->id_role ? 'selected' : '' }}>
                                                    {{ $role->role_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label>Numero de Telefono</label>
                                        <input type="text" name="phone" class="form-control" value="{{ $user->phone }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Nueva contraseña (opcional)</label>
                                        <input type="password" name="password" class="form-control" autocomplete="new-password">
                                        <ul id="password-requirements-{{ $user->id_user }}" class="mb-3 pl-3" style="list-style: none; padding-left: 1rem;">
                                        <li class="req-length text-muted">Mínimo 8 caracteres</li>
                                        <li class="req-special text-muted">Al menos un carácter especial (!@#...)</li>
                                        <li class="req-number text-muted">Al menos un número</li>
                                    </ul>

                                    </div>
                                    <div class="mb-3">
                                        <label>Confirmar contraseña</label>
                                        <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-warning">Actualizar</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Registrar -->
<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="full_name" class="form-control" required value="{{ old('full_name') }}">
                    </div>
                    <div class="mb-3">
                        <label>Correo</label>
                        <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                    </div>
                    <div class="mb-3">
                        <label>Rol</label>
                        <select name="id_role" class="form-control" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id_role }}">{{ $role->role_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Numero Telefono</label>
                        <input type="text" name="phone" class="form-control" required value="{{ old('phone') }}">
                    </div>
                    <div class="mb-3">
                        <label>Contraseña</label>
                        <input type="password" name="password" class="form-control" required autocomplete="new-password">
                        <ul id="password-requirements-register" class="mb-3 pl-3">
                        <li class="req-length text-muted">Mínimo 8 caracteres</li>
                        <li class="req-special text-muted">Al menos un carácter especial (!@#...)</li>
                        <li class="req-number text-muted">Al menos un número</li>
                    </ul>

                    </div>
                    <div class="mb-3">
                        <label>Confirmar Contraseña</label>
                        <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('css')
{{-- Estilos adicionales aquí --}}
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll("input[name='password']").forEach((input) => {
        const modal = input.closest('.modal');
        if (!modal) return;

        const reqList = modal.querySelector('ul');
        if (!reqList) return;

        const reqs = reqList.querySelectorAll('li');
        if (reqs.length !== 3) return;

        input.addEventListener('input', function () {
            const val = this.value;
            const length = val.length >= 8;
            const number = /\d/.test(val);
            const special = /[!@#$%^&*(),.?":{}|<>_\-+=]/.test(val);

            updateRequirement(reqs[0], length);
            updateRequirement(reqs[1], special);
            updateRequirement(reqs[2], number);
        });

        function updateRequirement(element, valid) {
            element.classList.remove('text-success', 'text-muted');
            element.classList.add(valid ? 'text-success' : 'text-muted');
        }
    });
});

</script>


@if(session('success') && session('alert_type'))
<script>
    let icon = 'success';
    let title = 'Éxito';
    let color = '#3085d6';

    switch ("{{ session('alert_type') }}") {
        case 'activado':
            icon = 'success';
            title = 'Usuario Activado';
            color = '#28a745';
            break;
        case 'desactivado':
            icon = 'warning';
            title = 'Usuario Desactivado';
            color = '#ffc107';
            break;
        case 'actualizado':
            icon = 'info';
            title = 'Usuario Actualizado';
            color = '#17a2b8';
            break;
        case 'creado':
        icon = 'success';
        title = 'Usuario Registrado';
        color = '#007bff';
        break;  
    }

    Swal.fire({
        icon: icon,
        title: title,
        text: '{{ session('success') }}',
        confirmButtonColor: color,
        confirmButtonText: 'OK'
    });
</script>
@endif



@if (session('error_password'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'error',
            title: 'Contraseña inválida',
            text: '{{ session('error_password') }}',
            confirmButtonColor: '#d33'
        });

        const modal = new bootstrap.Modal(document.getElementById('modalRegistrar'));
        modal.show();
    });
</script>
@endif

@if ($errors->has('email'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'error',
            title: 'Correo duplicado',
            text: '{{ $errors->first('email') }}',
            confirmButtonColor: '#d33'
        });

        const modal = new bootstrap.Modal(document.getElementById('modalRegistrar'));
        modal.show();
    });
</script>
@endif

@if (session('show_register_modal'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = new bootstrap.Modal(document.getElementById('modalRegistrar'));
        modal.show();
    });
</script>
@endif



@stop
