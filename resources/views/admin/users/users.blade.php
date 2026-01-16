@extends('adminlte::page')

@section('title', 'Gestión de Usuarios')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
  <h1 class="mb-0">Gestión de Usuarios</h1>
  <div class="d-flex gap-2">
    @can('create-users')
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
      <i class="fas fa-plus me-1"></i> Añadir Usuario
    </button>
    @endcan
    @can('force-delete-users')
    <a href="{{ route('admin.users.trashed') }}" class="btn btn-warning">
      <i class="fas fa-trash-restore me-1"></i> Ver Eliminados
    </a>
    @endcan
  </div>
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
            <td>{{ $user->getRoleNames()->first() ?? 'Sin rol' }}</td>
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
                @can('edit-users')
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditarDesktop{{ $user->user_id }}" title="Editar">
                  <i class="fas fa-edit"></i>
                </button>
                @endcan
                @can('soft-delete-users')
                <button type="button" class="btn btn-danger delete-user-btn"
                  data-url="{{ route('admin.users.destroy', $user->user_id) }}"
                  data-name="{{ $user->full_name }}"
                  title="Eliminar">
                  <i class="fas fa-trash"></i>
                </button>
                @endcan
                @can('edit-users')
                <button type="button" class="btn {{ $user->is_locked ? 'btn-secondary' : 'btn-warning' }} toggle-lock-btn"
                  data-url="{{ $user->is_locked ? route('admin.users.unlock', $user->user_id) : route('admin.users.lock', $user->user_id) }}"
                  data-locked="{{ $user->is_locked ? 1 : 0 }}"
                  data-name="{{ $user->full_name }}"
                  title="{{ $user->is_locked ? 'Desbloquear' : 'Bloquear' }}">
                  <i class="fas {{ $user->is_locked ? 'fa-unlock' : 'fa-lock' }}"></i>
                </button>
                @endcan
                @if (empty($user->email_verified_at))
                <button type="button" class="btn btn-info verify-email-btn"
                  data-url="{{ route('admin.users.markVerified', $user->user_id) }}"
                  data-name="{{ $user->full_name }}"
                  title="Marcar como verificado">
                  <i class="fas fa-check-circle"></i>
                </button>
                @endif
                @if ($user->two_factor_secret)
                <button type="button" class="btn btn-danger disable-2fa-btn"
                  data-url="{{ route('admin.users.disable2FA', $user->user_id) }}"
                  data-name="{{ $user->full_name }}"
                  title="Desactivar 2FA">
                  <i class="fas fa-shield-alt"></i>
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
            <span class="badge bg-primary">{{ $user->getRoleNames()->first() ?? 'Sin rol' }}</span>
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
          <button type="button" class="btn btn-sm btn-danger delete-user-btn"
            data-url="{{ route('admin.users.destroy', $user->user_id) }}"
            data-name="{{ $user->full_name }}">
            <i class="fas fa-trash me-1"></i>Eliminar
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
          @if ($user->two_factor_secret)
          <button type="button" class="btn btn-sm btn-danger disable-2fa-btn"
            data-url="{{ route('admin.users.disable2FA', $user->user_id) }}"
            data-name="{{ $user->full_name }}">
            <i class="fas fa-shield-alt me-1"></i>Desactivar 2FA
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
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Nombre <span class="text-danger">*</span></label>
              <input type="text" name="first_name" class="form-control" required value="{{ old('first_name') }}">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Apellido <span class="text-danger">*</span></label>
              <input type="text" name="last_name" class="form-control" required value="{{ old('last_name') }}">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
          </div>
          <div class="mb-3">
            <label class="form-label">Rol <span class="text-danger">*</span></label>
            <select name="role_id" class="form-select" required>
              @foreach ($roles as $role)
              <option value="{{ $role->id }}">{{ $role->name }}</option>
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
          
          {{-- Dirección --}}
          <div class="mb-3">
             <label class="form-label">Dirección</label>
             <input type="text" name="address" class="form-control" value="{{ old('address') }}" placeholder="Dirección exacta">
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
               <label class="form-label">Ciudad</label>
               <input type="text" name="city" class="form-control" value="{{ old('city') }}">
            </div>
            <div class="col-md-6 mb-3">
               <label class="form-label">Estado / Provincia</label>
               <input type="text" name="state" class="form-control" value="{{ old('state') }}">
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
               <label class="form-label">Código Postal (ZIP)</label>
               <input type="text" name="zip" class="form-control" value="{{ old('zip') }}">
            </div>
            <div class="col-md-6 mb-3">
               <label class="form-label">País</label>
               <select name="country" class="form-select">
                  <option value="">-- Seleccionar --</option>
                  <option value="CR" {{ old('country') === 'CR' ? 'selected' : '' }}>Costa Rica</option>
                  <option value="US" {{ old('country') === 'US' ? 'selected' : '' }}>United States</option>
                  <option value="PA" {{ old('country') === 'PA' ? 'selected' : '' }}>Panama</option>
                  <option value="NI" {{ old('country') === 'NI' ? 'selected' : '' }}>Nicaragua</option>
                  <option value="GT" {{ old('country') === 'GT' ? 'selected' : '' }}>Guatemala</option>
                  <option value="MX" {{ old('country') === 'MX' ? 'selected' : '' }}>Mexico</option>
                  <option value="CA" {{ old('country') === 'CA' ? 'selected' : '' }}>Canada</option>
               </select>
            </div>
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
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Nombre</label>
              <input type="text" name="first_name" class="form-control" value="{{ $user->first_name }}" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Apellido</label>
              <input type="text" name="last_name" class="form-control" value="{{ $user->last_name }}" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Rol</label>
            <select name="role_id" class="form-select" required>
              @foreach ($roles as $role)
              <option value="{{ $role->id }}" {{ $user->getRoleNames()->contains($role->name) ? 'selected' : '' }}>
                {{ $role->name }}
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
          
          {{-- Dirección --}}
          <div class="mb-3">
             <label class="form-label">Dirección</label>
             <input type="text" name="address" class="form-control" value="{{ $user->address }}">
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
               <label class="form-label">Ciudad</label>
               <input type="text" name="city" class="form-control" value="{{ $user->city }}">
            </div>
            <div class="col-md-6 mb-3">
               <label class="form-label">Estado / Provincia</label>
               <input type="text" name="state" class="form-control" value="{{ $user->state }}">
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
               <label class="form-label">Código Postal (ZIP)</label>
               <input type="text" name="zip" class="form-control" value="{{ $user->zip }}">
            </div>
            <div class="col-md-6 mb-3">
               <label class="form-label">País</label>
               <select name="country" class="form-select">
                  <option value="">-- Seleccionar --</option>
                  <option value="CR" {{ ($user->country ?? 'CR') === 'CR' ? 'selected' : '' }}>Costa Rica</option>
                  <option value="US" {{ $user->country === 'US' ? 'selected' : '' }}>United States</option>
                  <option value="PA" {{ $user->country === 'PA' ? 'selected' : '' }}>Panama</option>
                  <option value="NI" {{ $user->country === 'NI' ? 'selected' : '' }}>Nicaragua</option>
                  <option value="GT" {{ $user->country === 'GT' ? 'selected' : '' }}>Guatemala</option>
                  <option value="MX" {{ $user->country === 'MX' ? 'selected' : '' }}>Mexico</option>
                  <option value="CA" {{ $user->country === 'CA' ? 'selected' : '' }}>Canada</option>
               </select>
            </div>
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
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Nombre</label>
              <input type="text" name="first_name" class="form-control" value="{{ $user->first_name }}" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Apellido</label>
              <input type="text" name="last_name" class="form-control" value="{{ $user->last_name }}" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Rol</label>
            <select name="role_id" class="form-select" required>
              @foreach ($roles as $role)
              <option value="{{ $role->id }}" {{ $user->getRoleNames()->contains($role->name) ? 'selected' : '' }}>
                {{ $role->name }}
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
          
          {{-- Dirección --}}
          <div class="mb-3">
             <label class="form-label">Dirección</label>
             <input type="text" name="address" class="form-control" value="{{ $user->address }}">
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
               <label class="form-label">Ciudad</label>
               <input type="text" name="city" class="form-control" value="{{ $user->city }}">
            </div>
            <div class="col-md-6 mb-3">
               <label class="form-label">Estado / Provincia</label>
               <input type="text" name="state" class="form-control" value="{{ $user->state }}">
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
               <label class="form-label">Código Postal (ZIP)</label>
               <input type="text" name="zip" class="form-control" value="{{ $user->zip }}">
            </div>
            <div class="col-md-6 mb-3">
               <label class="form-label">País</label>
               <select name="country" class="form-select">
                  <option value="">-- Seleccionar --</option>
                  <option value="CR" {{ ($user->country ?? 'CR') === 'CR' ? 'selected' : '' }}>Costa Rica</option>
                  <option value="US" {{ $user->country === 'US' ? 'selected' : '' }}>United States</option>
                  <option value="PA" {{ $user->country === 'PA' ? 'selected' : '' }}>Panama</option>
                  <option value="NI" {{ $user->country === 'NI' ? 'selected' : '' }}>Nicaragua</option>
                  <option value="GT" {{ $user->country === 'GT' ? 'selected' : '' }}>Guatemala</option>
                  <option value="MX" {{ $user->country === 'MX' ? 'selected' : '' }}>Mexico</option>
                  <option value="CA" {{ $user->country === 'CA' ? 'selected' : '' }}>Canada</option>
               </select>
            </div>
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
<form id="deleteForm" method="POST" style="display:none;">@csrf @method('DELETE')</form>
<form id="lockForm" method="POST" style="display:none;">@csrf @method('PATCH')</form>
<form id="verifyForm" method="POST" style="display:none;">@csrf @method('PATCH')</form>
<form id="disable2FAForm" method="POST" style="display:none;">@csrf @method('PATCH')</form>

@stop

@section('css')
<style>
  /* ayuda a que los select no se achiquen */
  .minw-200 {
    min-width: 200px
  }

  .password-wrapper {
    position: relative
  }

  .toggle-password-abs {
    position: absolute;
    top: 50%;
    right: .75rem;
    transform: translateY(-50%);
    opacity: .7;
    cursor: pointer
  }

  .toggle-password-abs:hover {
    opacity: 1
  }

  .password-reqs li {
    margin: .25rem 0
  }

  .card {
    transition: box-shadow .2s
  }

  .card:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, .1)
  }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  const confirm = (title, text) => Swal.fire({
    title,
    text,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, continuar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#dc3545'
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

    // Eliminar usuario (soft delete)
    document.querySelectorAll('.delete-user-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const url = this.dataset.url;
        const name = this.dataset.name;
        confirm('¿Eliminar usuario?', `${name} será eliminado. Podrá ser restaurado desde la papelera.`)
          .then(r => {
            if (r.isConfirmed) {
              document.getElementById('deleteForm').action = url;
              document.getElementById('deleteForm').submit();
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

    // Desactivar 2FA
    document.querySelectorAll('.disable-2fa-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const url = this.dataset.url;
        const name = this.dataset.name;
        confirm('¿Desactivar 2FA?', `Se desactivará la autenticación de dos factores para ${name}`)
          .then(r => {
            if (r.isConfirmed) {
              document.getElementById('disable2FAForm').action = url;
              document.getElementById('disable2FAForm').submit();
            }
          });
      });
    });

    // Flash
    @if(session('success'))
    Swal.fire({
      icon: 'success',
      title: 'Éxito',
      text: @json(session('success'))
    });
    @endif
    @if(session('error'))
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: @json(session('error'))
    });
    @endif
  });
</script>
@stop