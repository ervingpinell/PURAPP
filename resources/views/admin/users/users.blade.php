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

    {{-- FILTROS --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}">
                {{-- Filtro por rol --}}
                <div class="row justify-content-center mb-2">
                    <div class="col-md-4 text-center">
                        <label for="rol" class="form-label">Filtrar por rol:</label>
                        <select name="rol" id="rol" class="form-select text-center">
                            <option value="">-- Todos --</option>
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->role_id }}" {{ request('rol') == $rol->role_id ? 'selected' : '' }}>
                                    {{ $rol->role_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- Filtro por estado --}}
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
                {{-- Filtro por correo --}}
                <div class="row justify-content-center mb-2">
                    <div class="col-md-4 text-center">
                        <label for="email" class="form-label">Filtrar por correo:</label>
                        <input type="email" name="email" id="email" class="form-control text-center"
                               placeholder="ejemplo@correo.com" value="{{ request('email') }}">
                    </div>
                </div>

                {{-- Botones --}}
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

{{-- TABLA --}}
<table class="table table-striped table-bordered table-hover">
    <thead class="bg-primary text-white">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Teléfono</th>
            <th>Estado</th>
            <th>Verificado</th> {{-- nueva columna --}}
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($users as $user)
        <tr>
            <td>{{ $user->user_id }}</td>
            <td>{{ $user->full_name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->role->role_name ?? 'Sin rol' }}</td>
            <td>{{ trim(($user->country_code ?? '').' '.($user->phone ?? '')) }}</td>
            <td>
                @if ($user->status)
                    <span class="badge bg-success">Activo</span>
                @else
                    <span class="badge bg-secondary">Inactivo</span>
                @endif
            </td>
            <td>
                @if ($user->email_verified_at)
                    <span class="badge bg-success">Sí</span>
                @else
                    <span class="badge bg-danger">No</span>
                @endif
            </td>
            <td>
                {{-- Editar --}}
                <a href="#" class="btn btn-edit btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $user->user_id }}">
                    <i class="fas fa-edit"></i>
                </a>

                {{-- Activar / Desactivar con SweetAlert --}}
                <form action="{{ route('admin.users.destroy', $user->user_id) }}" method="POST" class="d-inline js-status-form"
                      data-question="{{ $user->status ? '¿Deseas desactivar este usuario?' : '¿Deseas reactivar este usuario?' }}"
                      data-confirm="{{ $user->status ? 'Sí, desactivar' : 'Sí, reactivar' }}"
                      data-success="{{ $user->status ? 'Usuario desactivado' : 'Usuario reactivado' }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm {{ $user->status ? 'btn-delete' : 'btn-view' }}">
                        <i class="fas {{ $user->status ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                    </button>
                </form>

                {{-- Desbloquear (solo si está bloqueado) --}}
                @if(!empty($user->is_locked) && $user->is_locked)
                    <form action="{{ route('admin.users.unlock', $user->user_id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-warning">
                            <i class="fas fa-unlock"></i> Desbloquear
                        </button>
                    </form>
                @endif

                {{-- Reenviar verificación de correo --}}
                <form method="POST" action="{{ route('admin.users.resendVerification', $user->user_id) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-paper-plane"></i> Reenviar verificación
                    </button>
                </form>
            </td>
        </tr>

        {{-- Modal Editar --}}
        <div class="modal fade" id="modalEditar{{ $user->user_id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('admin.users.update', $user->user_id) }}" method="POST">
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
                                <select name="role_id" class="form-control" required>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->role_id }}" {{ $user->role_id == $role->role_id ? 'selected' : '' }}>
                                            {{ $role->role_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Código de país + Teléfono --}}
                            <div class="mb-3">
                                <label>Código de país</label>
                                <select name="country_code" class="form-select" required>
                                    @include('partials.country-codes', [
                                        'selected'  => $user->country_code,
                                        'showNames' => true
                                    ])
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Número de Teléfono</label>
                                <input type="text" name="phone" class="form-control" value="{{ $user->phone }}" required>
                            </div>

                            {{-- Password opcional --}}
                            <div class="mb-3">
                                <label>{{ __('adminlte::validation.attributes.password') }}</label>
                                <div class="password-wrapper">
                                    <input type="password" name="password" class="form-control password-input" autocomplete="new-password">
                                    <i class="fas fa-eye toggle-password-abs" role="button" aria-label="Mostrar/Ocultar"></i>
                                </div>
                                <ul class="password-reqs list-unstyled small ms-1 mt-2">
                                    <li data-rule="length"  class="text-muted">Al menos 8 caracteres</li>
                                    <li data-rule="special" class="text-muted">1 carácter especial ( .,!@#$%^&*()_+- )</li>
                                    <li data-rule="number"  class="text-muted">1 número</li>
                                </ul>
                            </div>
                            <div class="mb-3">
                                <label>{{ __('adminlte::validation.attributes.password_confirmation') }}</label>
                                <div class="password-wrapper">
                                    <input type="password" name="password_confirmation" class="form-control password-confirm-input" autocomplete="new-password">
                                    <i class="fas fa-eye toggle-password-abs" role="button" aria-label="Mostrar/Ocultar"></i>
                                </div>
                                <ul class="password-reqs list-unstyled small ms-1 mt-2">
                                    <li data-rule="match" class="text-muted">Las contraseñas coinciden</li>
                                </ul>
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

{{-- Modal Registrar --}}
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
                        <select name="role_id" class="form-control" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->role_id }}">{{ $role->role_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Código de país + Teléfono --}}
                    <div class="mb-3">
                        <label>Código de país</label>
                        <select name="country_code" class="form-select" required>
                            @include('partials.country-codes', [
                                'selected'  => old('country_code'),
                                'showNames' => true
                            ])
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Número Teléfono</label>
                        <input type="text" name="phone" class="form-control" required value="{{ old('phone') }}">
                    </div>

                    <div class="mb-3">
                        <label>{{ __('adminlte::validation.attributes.password') }}</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" class="form-control password-input" required autocomplete="new-password">
                            <i class="fas fa-eye toggle-password-abs" role="button" aria-label="Mostrar/Ocultar"></i>
                        </div>
                        <ul class="password-reqs list-unstyled small ms-1 mt-2">
                            <li data-rule="length"  class="text-muted">Al menos 8 caracteres</li>
                            <li data-rule="special" class="text-muted">1 carácter especial ( .,!@#$%^&*()_+- )</li>
                            <li data-rule="number"  class="text-muted">1 número</li>
                        </ul>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('adminlte::validation.attributes.password_confirmation') }}</label>
                        <div class="password-wrapper">
                            <input type="password" name="password_confirmation" class="form-control password-confirm-input" required autocomplete="new-password">
                            <i class="fas fa-eye toggle-password-abs" role="button" aria-label="Mostrar/Ocultar"></i>
                        </div>
                        <ul class="password-reqs list-unstyled small ms-1 mt-2">
                            <li data-rule="match" class="text-muted">Las contraseñas coinciden</li>
                        </ul>
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
<style>
/* Icono ojo dentro del input */
.password-wrapper { position: relative; }
.password-wrapper .toggle-password-abs {
    position: absolute; top: 50%; right: .75rem; transform: translateY(-50%);
    opacity: .7; cursor: pointer; pointer-events: auto;
}
.password-wrapper .toggle-password-abs:hover { opacity: 1; }
/* Alinear bullets de requisitos */
.password-reqs li { margin: .15rem 0; }
</style>
@stop

@section('js')
{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Toggle ojo (mostrar/ocultar)
    document.body.addEventListener('click', function (e) {
        const icon = e.target.closest('.toggle-password-abs');
        if (!icon) return;
        const input = icon.previousElementSibling;
        if (!input) return;
        input.type = input.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
        input.focus();
    });

    // Validación en vivo (requisitos + match)
    function rulesFor(value){ return {
        length: value.length >= 8,
        number: /\d/.test(value),
        special: /[!@#$%^&*(),.?":{}|<>_\-+=]/.test(value),
    }; }
    function paintReqs(listEl, rules){
        if (!listEl) return;
        listEl.querySelectorAll('li').forEach(li => {
            const r = li.dataset.rule;
            const ok = (r === 'match') ? !!rules.match : !!rules[r];
            li.classList.toggle('text-success', ok);
            li.classList.toggle('text-muted', !ok);
        });
    }
    function wireModal(modal){
        const pass = modal.querySelector('.password-input');
        theConf = modal.querySelector('.password-confirm-input');
        const passReq = pass ? pass.closest('.mb-3').querySelector('.password-reqs') : null;
        const confReq = theConf ? theConf.closest('.mb-3').querySelector('.password-reqs') : null;
        function refresh(){
            const p = pass ? pass.value : '';
            const c = theConf ? theConf.value : '';
            const base = rulesFor(p);
            paintReqs(passReq, base);
            if (confReq) paintReqs(confReq, Object.assign({}, base, { match: (p.length||c.length) ? (p === c && p.length>0) : false }));
        }
        pass && pass.addEventListener('input', refresh);
        theConf && theConf.addEventListener('input', refresh);
        refresh();
    }
    document.querySelectorAll('.modal').forEach(wireModal);
    document.addEventListener('shown.bs.modal', e => wireModal(e.target));

    // Confirmación SweetAlert para activar/desactivar
    document.querySelectorAll('.js-status-form').forEach(form => {
        form.addEventListener('submit', function(ev){
            ev.preventDefault();
            const q = form.dataset.question || '¿Confirmar acción?';
            const okText = form.dataset.confirm || 'Sí, confirmar';
            Swal.fire({
                icon: 'question',
                title: 'Confirmación',
                text: q,
                showCancelButton: true,
                confirmButtonText: okText,
                cancelButtonText: 'Cancelar'
            }).then(res => {
                if (res.isConfirmed) form.submit();
            });
        });
    });

    // Alertas de sesión (éxito / error)
    @if(session('success'))
        Swal.fire({ icon:'success', title:'Éxito', text:@json(session('success')) });
    @endif
    @if(session('error'))
        Swal.fire({ icon:'error', title:'Error', text:@json(session('error')) });
    @endif

    // Si hubo error de validación en registro, reabrimos modal (opcional)
    @if ($errors->any() && session('show_register_modal'))
        const m = new bootstrap.Modal(document.getElementById('modalRegistrar'));
        m.show();
    @endif
});
</script>
@stop
