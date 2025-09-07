{{-- resources/views/admin/users/users.blade.php --}}
@extends('adminlte::page')

@section('title', __('m_users.title'))

@section('content_header')
    <h1>{{ __('m_users.title') }}</h1>
@stop

@section('content')
<div class="p-3 table-responsive">
    <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
        <i class="fas fa-plus"></i> {{ __('m_users.add_user') }}
    </a>

    {{-- FILTROS --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}">
                {{-- Filtro por rol --}}
                <div class="row justify-content-center mb-2">
                    <div class="col-md-4 text-center">
                        <label for="rol" class="form-label">{{ __('m_users.filters.role') }}</label>
                        <select name="rol" id="rol" class="form-select text-center">
                            <option value="">{{ __('m_users.filters.all') }}</option>
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
                        <label for="estado" class="form-label">{{ __('m_users.filters.state') }}</label>
                        <select name="estado" id="estado" class="form-select text-center">
                            <option value="">{{ __('m_users.filters.all') }}</option>
                            <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>{{ __('m_users.status.active') }}</option>
                            <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>{{ __('m_users.status.inactive') }}</option>
                        </select>
                    </div>
                </div>
                {{-- Filtro por correo --}}
                <div class="row justify-content-center mb-2">
                    <div class="col-md-4 text-center">
                        <label for="email" class="form-label">{{ __('m_users.filters.email') }}</label>
                        <input type="email" name="email" id="email" class="form-control text-center"
                               placeholder="{{ __('m_users.filters.email_placeholder') }}" value="{{ request('email') }}">
                    </div>
                </div>

                {{-- Botones --}}
                <div class="row justify-content-center">
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> {{ __('m_users.filters.search') }}
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times"></i> {{ __('m_users.filters.clear') }}
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
            <th>{{ __('m_users.table.id') }}</th>
            <th>{{ __('m_users.table.name') }}</th>
            <th>{{ __('m_users.table.email') }}</th>
            <th>{{ __('m_users.table.role') }}</th>
            <th>{{ __('m_users.table.phone') }}</th>
            <th>{{ __('m_users.table.status') }}</th>
            <th>{{ __('m_users.table.verified') }}</th>
            <th>{{ __('m_users.table.locked') }}</th>
            <th>{{ __('m_users.table.actions') }}</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($users as $user)
        <tr>
            <td>{{ $user->user_id }}</td>
            <td>{{ $user->full_name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->role->role_name ?? __('m_users.no_role') }}</td>
            <td>{{ trim(($user->country_code ?? '').' '.($user->phone ?? '')) }}</td>
            <td>
                @if ($user->status)
                    <span class="badge bg-success">{{ __('m_users.status.active') }}</span>
                @else
                    <span class="badge bg-secondary">{{ __('m_users.status.inactive') }}</span>
                @endif
            </td>

            {{-- Verificado: check / x --}}
            <td class="text-center align-middle">
              @if ($user->email_verified_at)
                <span class="badge bg-info" title="{{ __('m_users.verified.yes') }}">
                  <i class="fas fa-check-circle"></i>
                </span>
              @else
                <span class="badge bg-danger" title="{{ __('m_users.verified.no') }}">
                  <i class="fas fa-times-circle"></i>
                </span>
              @endif
            </td>

            {{-- Bloqueado: lock/unlock --}}
            <td>
                @if (!empty($user->is_locked) && $user->is_locked)
                    <span class="badge bg-warning"><i class="fas fa-lock"></i> {{ __('m_users.locked.yes') }}</span>
                @else
                    <span class="badge bg-secondary"><i class="fas fa-unlock"></i> {{ __('m_users.locked.no') }}</span>
                @endif
            </td>

            <td>
                {{-- Editar --}}
                <a href="#" class="btn btn-edit btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $user->user_id }}" title="{{ __('m_users.actions.edit') }}">
                    <i class="fas fa-edit"></i>
                </a>

                {{-- Activar / Desactivar --}}
                <form action="{{ route('admin.users.destroy', $user->user_id) }}" method="POST" class="d-inline js-confirm"
                      data-title="{{ __('m_users.dialog.title') }}"
                      data-question="{{ $user->status ? __('m_users.dialog.confirm_deactivate') : __('m_users.dialog.confirm_reactivate') }}"
                      data-confirm="{{ $user->status ? __('m_users.dialog.action_deactivate') : __('m_users.dialog.action_reactivate') }}"
                      data-cancel="{{ __('m_users.dialog.cancel') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm {{ $user->status ? 'btn-delete' : 'btn-view' }}"
                            title="{{ $user->status ? __('m_users.actions.deactivate') : __('m_users.actions.reactivate') }}">
                        <i class="fas {{ $user->status ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                    </button>
                </form>

                {{-- Bloquear / Desbloquear --}}
                @if(!empty($user->is_locked) && $user->is_locked)
                    <form action="{{ route('admin.users.unlock', $user->user_id) }}" method="POST" class="d-inline js-confirm"
                          data-title="{{ __('m_users.dialog.title') }}"
                          data-question="{{ __('m_users.dialog.confirm_unlock') }}"
                          data-confirm="{{ __('m_users.dialog.action_unlock') }}"
                          data-cancel="{{ __('m_users.dialog.cancel') }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-secondary" title="{{ __('m_users.actions.unlock') }}">
                            <i class="fas fa-unlock"></i>
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.users.lock', $user->user_id) }}" method="POST" class="d-inline js-confirm"
                          data-title="{{ __('m_users.dialog.title') }}"
                          data-question="{{ __('m_users.dialog.confirm_lock') }}"
                          data-confirm="{{ __('m_users.dialog.action_lock') }}"
                          data-cancel="{{ __('m_users.dialog.cancel') }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-warning" title="{{ __('m_users.actions.lock') }}">
                            <i class="fas fa-lock"></i>
                        </button>
                    </form>
                @endif

                {{-- (Opcional) Marcar verificado si NO lo está --}}
                @if (empty($user->email_verified_at))
                    <form method="POST" action="{{ route('admin.users.markVerified', $user->user_id) }}" class="d-inline js-confirm"
                          data-title="{{ __('m_users.dialog.title') }}"
                          data-question="{{ __('m_users.dialog.confirm_mark_verified') }}"
                          data-confirm="{{ __('m_users.dialog.action_mark_verified') }}"
                          data-cancel="{{ __('m_users.dialog.cancel') }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-info" title="{{ __('m_users.actions.mark_verified') }}">
                            <i class="fas fa-check-circle"></i>
                        </button>
                    </form>
                @endif
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
                            <h5 class="modal-title">{{ __('m_users.modals.edit_user') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('m_users.modals.close') }}"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>{{ __('m_users.form.full_name') }}</label>
                                <input type="text" name="full_name" class="form-control" value="{{ $user->full_name }}" required>
                            </div>
                            <div class="mb-3">
                                <label>{{ __('m_users.form.email') }}</label>
                                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                            </div>
                            <div class="mb-3">
                                <label>{{ __('m_users.form.role') }}</label>
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
                                <label>{{ __('m_users.form.country_code') }}</label>
                                <select name="country_code" class="form-select" required>
                                    @include('partials.country-codes', [
                                        'selected'  => $user->country_code,
                                        'showNames' => true
                                    ])
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>{{ __('m_users.form.phone_number') }}</label>
                                <input type="text" name="phone" class="form-control" value="{{ $user->phone }}" required>
                            </div>

                            {{-- Password opcional --}}
                            <div class="mb-3">
                                <label>{{ __('m_users.form.password') }}</label>
                                <div class="password-wrapper">
                                    <input type="password" name="password" class="form-control password-input" autocomplete="new-password">
                                    <i class="fas fa-eye toggle-password-abs" role="button" aria-label="{{ __('m_users.form.toggle_password') }}"></i>
                                </div>
                                <ul class="password-reqs list-unstyled small ms-1 mt-2">
                                    <li data-rule="length"  class="text-muted">{{ __('m_users.password_reqs.length') }}</li>
                                    <li data-rule="special" class="text-muted">{{ __('m_users.password_reqs.special') }}</li>
                                    <li data-rule="number"  class="text-muted">{{ __('m_users.password_reqs.number') }}</li>
                                </ul>
                            </div>
                            <div class="mb-3">
                                <label>{{ __('m_users.form.password_confirmation') }}</label>
                                <div class="password-wrapper">
                                    <input type="password" name="password_confirmation" class="form-control password-confirm-input" autocomplete="new-password">
                                    <i class="fas fa-eye toggle-password-abs" role="button" aria-label="{{ __('m_users.form.toggle_password') }}"></i>
                                </div>
                                <ul class="password-reqs list-unstyled small ms-1 mt-2">
                                    <li data-rule="match" class="text-muted">{{ __('m_users.password_reqs.match') }}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-warning">{{ __('m_users.modals.update') }}</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_users.modals.cancel') }}</button>
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
                    <h5 class="modal-title">{{ __('m_users.modals.register_user') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('m_users.modals.close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>{{ __('m_users.form.full_name') }}</label>
                        <input type="text" name="full_name" class="form-control" required value="{{ old('full_name') }}">
                    </div>
                    <div class="mb-3">
                        <label>{{ __('m_users.form.email') }}</label>
                        <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                    </div>
                    <div class="mb-3">
                        <label>{{ __('m_users.form.role') }}</label>
                        <select name="role_id" class="form-control" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->role_id }}">{{ $role->role_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Código de país + Teléfono --}}
                    <div class="mb-3">
                        <label>{{ __('m_users.form.country_code') }}</label>
                        <select name="country_code" class="form-select" required>
                            @include('partials.country-codes', [
                                'selected'  => old('country_code'),
                                'showNames' => true
                            ])
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('m_users.form.phone_number') }}</label>
                        <input type="text" name="phone" class="form-control" required value="{{ old('phone') }}">
                    </div>

                    <div class="mb-3">
                        <label>{{ __('m_users.form.password') }}</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" class="form-control password-input" required autocomplete="new-password">
                            <i class="fas fa-eye toggle-password-abs" role="button" aria-label="{{ __('m_users.form.toggle_password') }}"></i>
                        </div>
                        <ul class="password-reqs list-unstyled small ms-1 mt-2">
                            <li data-rule="length"  class="text-muted">{{ __('m_users.password_reqs.length') }}</li>
                            <li data-rule="special" class="text-muted">{{ __('m_users.password_reqs.special') }}</li>
                            <li data-rule="number"  class="text-muted">{{ __('m_users.password_reqs.number') }}</li>
                        </ul>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('m_users.form.password_confirmation') }}</label>
                        <div class="password-wrapper">
                            <input type="password" name="password_confirmation" class="form-control password-confirm-input" required autocomplete="new-password">
                            <i class="fas fa-eye toggle-password-abs" role="button" aria-label="{{ __('m_users.form.toggle_password') }}"></i>
                        </div>
                        <ul class="password-reqs list-unstyled small ms-1 mt-2">
                            <li data-rule="match" class="text-muted">{{ __('m_users.password_reqs.match') }}</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('m_users.modals.save') }}</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_users.modals.cancel') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('css')
<style>
.password-wrapper { position: relative; }
.password-wrapper .toggle-password-abs {
    position: absolute; top: 50%; right: .75rem; transform: translateY(-50%);
    opacity: .7; cursor: pointer; pointer-events: auto;
}
.password-wrapper .toggle-password-abs:hover { opacity: 1; }
.password-reqs li { margin: .15rem 0; }
</style>
@stop

@section('js')
{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Ojo pass
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

    // Confirmaciones i18n via data-*
    document.querySelectorAll('.js-confirm').forEach(form => {
        form.addEventListener('submit', function(ev){
            ev.preventDefault();
            const title  = form.dataset.title   || 'Confirmación';
            const q      = form.dataset.question|| '¿Confirmar acción?';
            const okText = form.dataset.confirm || 'Sí, confirmar';
            const cancel = form.dataset.cancel  || 'Cancelar';
            Swal.fire({
                icon: 'question',
                title: title,
                text: q,
                showCancelButton: true,
                confirmButtonText: okText,
                cancelButtonText: cancel
            }).then(res => { if (res.isConfirmed) form.submit(); });
        });
    });

    // Alertas de sesión (si las usas con traducciones desde el controlador)
    @if(session('success'))
        Swal.fire({ icon:'success', title:@json(__('m_users.alert.success')), text:@json(session('success')) });
    @endif
    @if(session('error'))
        Swal.fire({ icon:'error', title:@json(__('m_users.alert.error')), text:@json(session('error')) });
    @endif
});
</script>
@stop
