@extends('adminlte::page')

@section('title', 'Gestión de Usuarios')

@section('content_header')
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h1 class="mb-0">Gestión de Usuarios</h1>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
      <i class="fas fa-plus me-1"></i> Añadir Usuario
    </button>
  </div>
@stop

@section('content')
<div class="container-fluid py-3">

  {{-- ======================= Filtros (centrados como la captura) ======================= --}}
  <div class="card mb-3">
    <div class="card-body">
      <form method="GET" action="{{ route('admin.users.index') }}">
        <div class="d-flex flex-column align-items-center text-center gap-3">

          {{-- Fila: rol / estado --}}
          <div class="d-flex flex-wrap justify-content-center gap-4">
            <div class="minw-200">
              <div class="fw-semibold mb-1">Filtrar por rol:</div>
              <select name="rol" id="rol" class="form-select form-select-sm">
                <option value="">-- Todos --</option>
                @foreach ($roles as $rol)
                  <option value="{{ $rol->role_id }}" {{ request('rol') == $rol->role_id ? 'selected' : '' }}>
                    {{ $rol->role_name }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="minw-200">
              <div class="fw-semibold mb-1">Filtrar por estado:</div>
              <select name="estado" id="estado" class="form-select form-select-sm">
                <option value="">-- Todos --</option>
                <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>Activo</option>
                <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivo</option>
              </select>
            </div>
          </div>

          {{-- Fila: correo --}}
          <div class="w-100" style="max-width: 760px;">
            <div class="fw-semibold mb-1">Filtrar por correo:</div>
            <input type="email"
                   name="email"
                   id="email"
                   class="form-control"
                   placeholder="ejemplo@correo.com"
                   value="{{ request('email') }}">
          </div>

          {{-- Fila: botones (mismo ancho y centrados) --}}
          <div class="w-100" style="max-width: 760px;">
            <div class="row g-2">
              <div class="col-12 col-sm-6">
                <button type="submit" class="btn btn-primary w-100">
                  <i class="fas fa-search me-1"></i> Buscar
                </button>
              </div>
              <div class="col-12 col-sm-6">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">
                  <i class="fas fa-times me-1"></i> Limpiar
                </a>
              </div>
            </div>
          </div>

        </div>
      </form>
    </div>
  </div>
  {{-- ======================= /Filtros ======================= --}}

  {{-- Vista Desktop: Tabla --}}
  <div class="d-none d-lg-block">
    <div class="table-responsive">
      <table class="table table-striped table-bordered table-hover align-middle">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Teléfono</th>
            <th>Estado</th>
            <th>Verificado</th>
            <th>Bloqueado</th>
            <th style="width:200px;">Acciones</th>
          </tr>
        </thead>
        <tbody>
        @foreach ($users as $user)
          <tr>
            <td>{{ $user->user_id }}</td>
            <td><strong>{{ $user->full_name }}</strong></td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->role->role_name ?? 'Sin rol' }}</td>
            <td>{{ trim(($user->country_code ?? '').' '.($user->phone ?? '')) }}</td>
            <td>
              <span class="badge {{ $user->status ? 'bg-success' : 'bg-secondary' }}">
                {{ $user->status ? 'Activo' : 'Inactivo' }}
              </span>
            </td>
            <td class="text-center">
              @if ($user->email_verified_at)
                <span class="badge bg-info"><i class="fas fa-check-circle"></i></span>
              @else
                <span class="badge bg-danger"><i class="fas fa-times-circle"></i></span>
              @endif
            </td>
            <td class="text-center">
              @if (!empty($user->is_locked) && $user->is_locked)
                <span class="badge bg-warning"><i class="fas fa-lock"></i></span>
              @else
                <span class="badge bg-secondary"><i class="fas fa-unlock"></i></span>
              @endif
            </td>
            <td>
              <div class="btn-group btn-group-sm">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditarDesktop{{ $user->user_id }}" title="Editar">
                  <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn {{ $user->status ? 'btn-danger' : 'btn-success' }} toggle-status-btn"
                        data-url="{{ route('admin.users.destroy', $user->user_id) }}"
                        data-status="{{ $user->status }}"
                        data-name="{{ $user->full_name }}"
                        title="{{ $user->status ? 'Desactivar' : 'Activar' }}">
                  <i class="fas {{ $user->status ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                </button>
                <button type="button" class="btn {{ $user->is_locked ? 'btn-secondary' : 'btn-warning' }} toggle-lock-btn"
                        data-url="{{ $user->is_locked ? route('admin.users.unlock', $user->user_id) : route('admin.users.lock', $user->user_id) }}"
                        data-locked="{{ $user->is_locked ? 1 : 0 }}"
                        data-name="{{ $user->full_name }}"
                        title="{{ $user->is_locked ? 'Desbloquear' : 'Bloquear' }}">
                  <i class="fas {{ $user->is_locked ? 'fa-unlock' : 'fa-lock' }}"></i>
                </button>
                @if (empty($user->email_verified_at))
                  <button type="button" class="btn btn-info verify-email-btn"
                          data-url="{{ route('admin.users.markVerified', $user->user_id) }}"
                          data-name="{{ $user->full_name }}"
                          title="Marcar como verificado">
                    <i class="fas fa-check-circle"></i>
                  </button>
                @endif
              </div>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- Vista Mobile: Cards --}}
  <div class="d-lg-none">
    @foreach ($users as $user)
      <div class="card mb-3 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
              <h5 class="mb-1">{{ $user->full_name }}</h5>
              <p class="text-muted small mb-1">
                <i class="fas fa-envelope me-1"></i>{{ $user->email }}
              </p>
              <p class="text-muted small mb-0">
                <i class="fas fa-phone me-1"></i>{{ trim(($user->country_code ?? '').' '.($user->phone ?? '')) }}
              </p>
            </div>
            <div class="d-flex flex-column gap-1 align-items-end">
              <span class="badge {{ $user->status ? 'bg-success' : 'bg-secondary' }}">
                {{ $user->status ? 'Activo' : 'Inactivo' }}
              </span>
              <span class="badge bg-primary">{{ $user->role->role_name ?? 'Sin rol' }}</span>
            </div>
          </div>

          <div class="d-flex gap-2 mb-2">
            @if ($user->email_verified_at)
              <span class="badge bg-info"><i class="fas fa-check-circle me-1"></i>Verificado</span>
            @else
              <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>No verificado</span>
            @endif
            @if ($user->is_locked)
              <span class="badge bg-warning"><i class="fas fa-lock me-1"></i>Bloqueado</span>
            @endif
          </div>

          <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditarMobile{{ $user->user_id }}">
              <i class="fas fa-edit me-1"></i>Editar
            </button>
            <button type="button" class="btn btn-sm {{ $user->status ? 'btn-danger' : 'btn-success' }} toggle-status-btn"
                    data-url="{{ route('admin.users.destroy', $user->user_id) }}"
                    data-status="{{ $user->status }}"
                    data-name="{{ $user->full_name }}">
              <i class="fas {{ $user->status ? 'fa-user-slash' : 'fa-user-check' }} me-1"></i>
              {{ $user->status ? 'Desactivar' : 'Activar' }}
            </button>
            <button type="button" class="btn btn-sm {{ $user->is_locked ? 'btn-secondary' : 'btn-warning' }} toggle-lock-btn"
                    data-url="{{ $user->is_locked ? route('admin.users.unlock', $user->user_id) : route('admin.users.lock', $user->user_id) }}"
                    data-locked="{{ $user->is_locked ? 1 : 0 }}"
                    data-name="{{ $user->full_name }}">
              <i class="fas {{ $user->is_locked ? 'fa-unlock' : 'fa-lock' }} me-1"></i>
              {{ $user->is_locked ? 'Desbloquear' : 'Bloquear' }}
            </button>
            @if (empty($user->email_verified_at))
              <button type="button" class="btn btn-sm btn-info verify-email-btn"
                      data-url="{{ route('admin.users.markVerified', $user->user_id) }}"
                      data-name="{{ $user->full_name }}">
                <i class="fas fa-check-circle me-1"></i>Verificar
              </button>
            @endif
          </div>
        </div>
      </div>
    @endforeach
  </div>

</div>

{{-- Modal Registrar --}}
<div class="modal fade" id="modalRegistrar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form action="{{ route('admin.users.store') }}" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Registrar Usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
            <input type="text" name="full_name" class="form-control" required value="{{ old('full_name') }}">
          </div>
          <div class="mb-3">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
          </div>
          <div class="mb-3">
            <label class="form-label">Rol <span class="text-danger">*</span></label>
            <select name="role_id" class="form-select" required>
              @foreach ($roles as $role)
                <option value="{{ $role->role_id }}">{{ $role->role_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Código de país <span class="text-danger">*</span></label>
            <select name="country_code" class="form-select" required>
              @include('partials.country-codes', ['selected' => old('country_code'), 'showNames' => true])
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Teléfono <span class="text-danger">*</span></label>
            <input type="text" name="phone" class="form-control" required value="{{ old('phone') }}">
          </div>
          <div class="mb-3">
            <label class="form-label">Contraseña <span class="text-danger">*</span></label>
            <div class="password-wrapper">
              <input type="password" name="password" class="form-control password-input" required>
              <i class="fas fa-eye toggle-password-abs"></i>
            </div>
            <ul class="password-reqs list-unstyled small mt-2">
              <li data-rule="length" class="text-muted">Mínimo 8 caracteres</li>
              <li data-rule="special" class="text-muted">Al menos un carácter especial</li>
              <li data-rule="number" class="text-muted">Al menos un número</li>
            </ul>
          </div>
          <div class="mb-3">
            <label class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
            <div class="password-wrapper">
              <input type="password" name="password_confirmation" class="form-control password-confirm-input" required>
              <i class="fas fa-eye toggle-password-abs"></i>
            </div>
            <ul class="password-reqs list-unstyled small mt-2">
              <li data-rule="match" class="text-muted">Las contraseñas deben coincidir</li>
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

{{-- Modales de Edición Desktop --}}
@foreach ($users as $user)
<div class="modal fade" id="modalEditarDesktop{{ $user->user_id }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form action="{{ route('admin.users.update', $user->user_id) }}" method="POST">
      @csrf @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar Usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nombre completo</label>
            <input type="text" name="full_name" class="form-control" value="{{ $user->full_name }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Rol</label>
            <select name="role_id" class="form-select" required>
              @foreach ($roles as $role)
                <option value="{{ $role->role_id }}" {{ $user->role_id == $role->role_id ? 'selected' : '' }}>
                  {{ $role->role_name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Código de país</label>
            <select name="country_code" class="form-select" required>
              @include('partials.country-codes', ['selected' => $user->country_code, 'showNames' => true])
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" name="phone" class="form-control" value="{{ $user->phone }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Contraseña <small class="text-muted">(dejar vacío para no cambiar)</small></label>
            <div class="password-wrapper">
              <input type="password" name="password" class="form-control password-input">
              <i class="fas fa-eye toggle-password-abs"></i>
            </div>
            <ul class="password-reqs list-unstyled small mt-2">
              <li data-rule="length" class="text-muted">Mínimo 8 caracteres</li>
              <li data-rule="special" class="text-muted">Al menos un carácter especial</li>
              <li data-rule="number" class="text-muted">Al menos un número</li>
            </ul>
          </div>
          <div class="mb-3">
            <label class="form-label">Confirmar contraseña</label>
            <div class="password-wrapper">
              <input type="password" name="password_confirmation" class="form-control password-confirm-input">
              <i class="fas fa-eye toggle-password-abs"></i>
            </div>
            <ul class="password-reqs list-unstyled small mt-2">
              <li data-rule="match" class="text-muted">Las contraseñas deben coincidir</li>
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

{{-- Modales de Edición Mobile --}}
@foreach ($users as $user)
<div class="modal fade" id="modalEditarMobile{{ $user->user_id }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form action="{{ route('admin.users.update', $user->user_id) }}" method="POST">
      @csrf @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar Usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nombre completo</label>
            <input type="text" name="full_name" class="form-control" value="{{ $user->full_name }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Rol</label>
            <select name="role_id" class="form-select" required>
              @foreach ($roles as $role)
                <option value="{{ $role->role_id }}" {{ $user->role_id == $role->role_id ? 'selected' : '' }}>
                  {{ $role->role_name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Código de país</label>
            <select name="country_code" class="form-select" required>
              @include('partials.country-codes', ['selected' => $user->country_code, 'showNames' => true])
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" name="phone" class="form-control" value="{{ $user->phone }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Contraseña <small class="text-muted">(opcional)</small></label>
            <div class="password-wrapper">
              <input type="password" name="password" class="form-control password-input">
              <i class="fas fa-eye toggle-password-abs"></i>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Confirmar contraseña</label>
            <div class="password-wrapper">
              <input type="password" name="password_confirmation" class="form-control password-confirm-input">
              <i class="fas fa-eye toggle-password-abs"></i>
            </div>
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

{{-- Formularios ocultos --}}
<form id="statusForm" method="POST" style="display:none;">@csrf @method('DELETE')</form>
<form id="lockForm" method="POST" style="display:none;">@csrf @method('PATCH')</form>
<form id="verifyForm" method="POST" style="display:none;">@csrf @method('PATCH')</form>

@stop

@section('css')
<style>
/* ayuda a que los select no se achiquen */
.minw-200{min-width:200px}
.password-wrapper{position:relative}
.toggle-password-abs{
  position:absolute;top:50%;right:.75rem;transform:translateY(-50%);
  opacity:.7;cursor:pointer
}
.toggle-password-abs:hover{opacity:1}
.password-reqs li{margin:.25rem 0}
.card{transition:box-shadow .2s}
.card:hover{box-shadow:0 4px 8px rgba(0,0,0,.1)}
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const confirm = (title, text) => Swal.fire({
  title, text, icon: 'warning', showCancelButton: true,
  confirmButtonText: 'Sí, continuar', cancelButtonText: 'Cancelar', confirmButtonColor: '#dc3545'
});

document.addEventListener('DOMContentLoaded', () => {
  // Mostrar/Ocultar contraseña
  document.body.addEventListener('click', e => {
    const icon = e.target.closest('.toggle-password-abs');
    if (!icon) return;
    const input = icon.previousElementSibling;
    if (!input) return;
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
  });

  // Des/activar
  document.querySelectorAll('.toggle-status-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const url = this.dataset.url;
      const status = this.dataset.status === '1';
      const name = this.dataset.name;
      confirm(
        status ? '¿Desactivar usuario?' : '¿Activar usuario?',
        `${name} será ${status ? 'desactivado' : 'activado'}`
      ).then(r => {
        if (r.isConfirmed) {
          document.getElementById('statusForm').action = url;
          document.getElementById('statusForm').submit();
        }
      });
    });
  });

  // Bloquear/Desbloquear
  document.querySelectorAll('.toggle-lock-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const url = this.dataset.url;
      const locked = this.dataset.locked === '1';
      const name = this.dataset.name;
      confirm(
        locked ? '¿Desbloquear usuario?' : '¿Bloquear usuario?',
        `${name} será ${locked ? 'desbloqueado' : 'bloqueado'}`
      ).then(r => {
        if (r.isConfirmed) {
          document.getElementById('lockForm').action = url;
          document.getElementById('lockForm').submit();
        }
      });
    });
  });

  // Verificar email
  document.querySelectorAll('.verify-email-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const url = this.dataset.url;
      const name = this.dataset.name;
      confirm('¿Marcar como verificado?', `${name} será marcado con email verificado`)
        .then(r => {
          if (r.isConfirmed) {
            document.getElementById('verifyForm').action = url;
            document.getElementById('verifyForm').submit();
          }
        });
    });
  });

  // Flash
  @if(session('success'))
    Swal.fire({ icon:'success', title:'Éxito', text:@json(session('success')) });
  @endif
  @if(session('error'))
    Swal.fire({ icon:'error', title:'Error', text:@json(session('error')) });
  @endif
});
</script>
@stop
