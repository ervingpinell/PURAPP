@extends('adminlte::page')

@section('title', __('admin.roles.title'))

@section('content_header')
<h1>{{ __('admin.roles.title') }}</h1>
@stop

@section('css')
<style>
  .modal-top .modal-dialog {
    margin: 5.5vh auto 1.5rem !important;
  }

  .modal-elevated .modal-dialog {
    margin: 6vh auto 1.5rem !important;
  }

  .btn-toggle {
    min-width: 38px;
  }

  .table td.text-end,
  .table th.text-end {
    text-align: end;
  }

  /* Responsive Styles */
  @media (max-width: 767px) {

    .filter-row .col-lg-5,
    .filter-row .col-lg-3,
    .filter-row .col-lg-4 {
      margin-bottom: 0.5rem;
    }
  }
</style>
@endsection

@section('content')

<!-- Botón para crear nuevo rol -->
@can('create-roles')
<div class="mb-3">
  <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearRol">
    <i class="fas fa-plus"></i> {{ __('admin.roles.add_role') }}
  </a>
</div>
@endcan

@php
$curStatus = $status ?? request('status', 'all');
$isSortingById = ($sort ?? request('sort', 'name')) === 'id';
$currentDir = ($dir ?? request('dir', 'asc'));
$nextDir = $currentDir === 'asc' ? 'desc' : 'asc';
$iconDir = $currentDir === 'asc' ? 'fa-sort-numeric-down' : 'fa-sort-numeric-up-alt';
$urlSortId = route('admin.roles.index', [
'q' => $q ?? request('q'),
'status' => $curStatus,
'sort' => 'id',
'dir' => $isSortingById ? $nextDir : 'asc',
]);
$urlClear = route('admin.roles.index');
@endphp

{{-- Barra de búsqueda, filtro por estado y acciones --}}
<form method="GET" action="{{ route('admin.roles.index') }}" class="mb-3">
  <div class="row g-2 align-items-end filter-row">

    {{-- Buscar por nombre + lupa alineada --}}
    <div class="col-lg-5">
      <label for="q" class="form-label mb-1">{{ __('admin.roles.search_by_name') }}</label>
      <div class="input-group">
        <input type="text" name="q" id="q" class="form-control"
          placeholder="{{ __('admin.roles.search_placeholder') }}"
          value="{{ old('q', $q ?? request('q')) }}">
        <button type="submit" class="btn btn-primary" title="{{ __('admin.roles.search_by_name') }}">
          <i class="fas fa-search"></i>
        </button>
      </div>
    </div>

    {{-- Estado --}}
    <div class="col-lg-3">
      <label for="status" class="form-label mb-1">{{ __('admin.roles.status') }}</label>
      <select name="status" id="status" class="form-select">
        <option value="all" {{ $curStatus === 'all' ? 'selected' : '' }}>{{ __('admin.roles.all') }}</option>
        <option value="active" {{ $curStatus === 'active' ? 'selected' : '' }}>{{ __('admin.roles.active') }}</option>
        <option value="inactive" {{ $curStatus === 'inactive' ? 'selected' : '' }}>{{ __('admin.roles.inactive') }}</option>
      </select>
    </div>

    {{-- Preservar sort/dir actuales --}}
    <input type="hidden" name="sort" value="{{ $sort ?? request('sort', 'name') }}">
    <input type="hidden" name="dir" value="{{ $dir  ?? request('dir',  'asc')  }}">

    {{-- Acciones a la derecha: Ordenar por ID + Limpiar --}}
    <div class="col-lg-4 d-grid d-lg-flex justify-content-lg-end gap-2">
      <a href="{{ $urlSortId }}" class="btn btn-secondary" title="{{ __('admin.roles.sort_by_id') }}">
        <i class="fas {{ $isSortingById ? $iconDir : 'fa-sort' }}"></i>
        {{ __('admin.roles.sort_by_id') }}
        @if($isSortingById)
        <small class="text-muted">({{ strtoupper($currentDir) }})</small>
        @endif
      </a>
      <a href="{{ $urlClear }}" class="btn btn-outline-secondary" title="{{ __('admin.roles.clear_filters') }}">
        <i class="fas fa-eraser"></i> {{ __('admin.roles.clear_filters') }}
      </a>
    </div>

  </div>
</form>

<div class="card">
  {{-- Desktop Table View --}}
  <div class="card-body table-responsive p-0 d-none d-md-block">
    <table class="table table-hover text-nowrap mb-0">
      <thead class="thead-dark">
        <tr>
          <th>{{ __('admin.roles.role_name') }}</th>
          <th>{{ __('admin.roles.description') }}</th>
          <th>{{ __('admin.roles.status') }}</th>
          <th class="text-end">{{ __('admin.roles.actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($roles as $role)
        <tr>
          <td>{{ $role->name }}</td>
          <td>{{ $role->description ?? '—' }}</td>
          <td>
            <span class="badge {{ $role->is_active ? 'bg-success' : 'bg-secondary' }}">
              {{ $role->is_active ? __('admin.roles.active') : __('admin.roles.inactive') }}
            </span>
          </td>
          <td class="text-end">
            {{-- Permisos --}}
            @can('edit-roles')
            <a href="{{ route('admin.roles.permissions', $role->id) }}"
              class="btn btn-sm btn-info text-white"
              title="{{ __('admin.roles.manage_permissions') }}">
              <i class="fas fa-key"></i>
            </a>

            <button type="button"
              class="btn btn-sm btn-warning"
              data-bs-toggle="modal"
              data-bs-target="#modalEditarRol{{ $role->id }}"
              title="{{ __('admin.roles.edit') }}">
              <i class="fas fa-edit"></i>
            </button>

            @endcan
            @can('publish-roles')
            <form action="{{ route('admin.roles.toggle', $role->id) }}"
              method="POST"
              class="d-inline toggle-form">
              @csrf
              @method('PATCH')

              <input type="hidden" name="q" value="{{ $q ?? request('q') }}">
              <input type="hidden" name="status" value="{{ $curStatus }}">
              <input type="hidden" name="sort" value="{{ $sort ?? request('sort','name') }}">
              <input type="hidden" name="dir" value="{{ $dir ?? request('dir','asc') }}">

              <button type="submit"
                class="btn btn-sm {{ $role->is_active ? 'btn-success' : 'btn-secondary' }}"
                title="{{ $role->is_active ? __('admin.roles.deactivate') : __('admin.roles.activate') }}">
                <i class="fas {{ $role->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
              </button>
            </form>
            @endcan
          </td>
        </tr>

        {{-- Modal Editar Rol --}}
        <div class="modal fade modal-top" id="modalEditarRol{{ $role->id }}" tabindex="-1" aria-labelledby="modalEditarRolLabel{{ $role->id }}" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                  <h5 class="modal-title" id="modalEditarRolLabel{{ $role->id }}">{{ __('admin.roles.edit_role') }}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('admin.roles.cancel') }}"></button>
                </div>

                <div class="modal-body">
                  <div class="mb-3">
                    <label for="role_name_{{ $role->id }}" class="form-label">{{ __('admin.roles.role_name') }}</label>
                    <input type="text"
                      name="role_name"
                      id="role_name_{{ $role->id }}"
                      class="form-control @error('role_name') is-invalid @enderror"
                      value="{{ old('role_name', $role->name) }}"
                      required>
                    @error('role_name')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                  </div>

                  <div class="mb-3">
                    <label for="description_{{ $role->id }}" class="form-label">{{ __('admin.roles.description') }}</label>
                    <textarea name="description"
                      id="description_{{ $role->id }}"
                      class="form-control @error('description') is-invalid @enderror"
                      rows="3">{{ old('description', $role->description) }}</textarea>
                    @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                  </div>
                </div>

                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin.roles.cancel') }}</button>
                  <button type="submit" class="btn btn-primary">{{ __('admin.roles.save_changes') }}</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        @empty
        <tr>
          <td colspan="5" class="text-center text-muted py-4">{{ __('admin.roles.no_roles') }}</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Mobile Card View --}}
  <div class="card-body d-md-none">
    @forelse ($roles as $role)
    <div class="card mb-3 shadow-sm">
      <div class="card-header bg-{{ $role->is_active ? 'info' : 'secondary' }} text-white d-flex justify-content-between align-items-center">
        <strong>{{ $role->name }}</strong>
        <span class="badge bg-light text-dark">#{{ $role->id }}</span>
      </div>
      <div class="card-body">
        <p class="mb-2"><strong>{{ __('admin.roles.description') }}:</strong> {{ $role->description ?? __('admin.roles.no_description') }}</p>
        <p class="mb-3">
          <strong>{{ __('admin.roles.status') }}:</strong>
          <span class="badge {{ $role->is_active ? 'bg-success' : 'bg-secondary' }}">
            {{ $role->is_active ? __('admin.roles.active') : __('admin.roles.inactive') }}
          </span>
        </p>

        <div class="d-grid gap-2">
          @can('edit-roles')
          <a href="{{ route('admin.roles.permissions', $role->id) }}" class="btn btn-info text-white btn-sm">
            <i class="fas fa-key me-1"></i> {{ __('admin.roles.manage_permissions') }}
          </a>

          <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditarRol{{ $role->id }}">
            <i class="fas fa-edit me-1"></i> {{ __('admin.roles.edit') }}
          </button>

          <form action="{{ route('admin.roles.toggle', $role->id) }}" method="POST" class="toggle-form">
            @csrf
            @method('PATCH')
            <input type="hidden" name="q" value="{{ $q ?? request('q') }}">
            <input type="hidden" name="status" value="{{ $curStatus }}">
            <input type="hidden" name="sort" value="{{ $sort ?? request('sort','name') }}">
            <input type="hidden" name="dir" value="{{ $dir ?? request('dir','asc') }}">

            <button type="submit" class="btn btn-sm {{ $role->is_active ? 'btn-success' : 'btn-secondary' }} w-100">
              <i class="fas {{ $role->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }} me-1"></i>
              {{ $role->is_active ? __('admin.roles.deactivate') : __('admin.roles.activate') }}
            </button>
          </form>
          @endcan
        </div>
      </div>
    </div>
    @empty
    <div class="alert alert-info text-center">
      <i class="fas fa-info-circle"></i> {{ __('admin.roles.no_roles') }}
    </div>
    @endforelse
  </div>
</div>

<!-- Modal Crear Rol -->
<div class="modal fade modal-elevated" id="modalCrearRol" tabindex="-1" aria-labelledby="modalCrearRolLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('admin.roles.store') }}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="modalCrearRolLabel">{{ __('admin.roles.create_role') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('admin.roles.cancel') }}"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="role_name" class="form-label">{{ __('admin.roles.role_name') }}</label>
          <input type="text" name="role_name" id="role_name" class="form-control" required maxlength="50">
        </div>
        <div class="mb-3">
          <label for="description" class="form-label">{{ __('admin.roles.description') }} ({{ __('admin.roles.optional') }})</label>
          <textarea name="description" id="description" class="form-control" rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin.roles.cancel') }}</button>
        <button type="submit" class="btn btn-success">{{ __('admin.roles.save') }}</button>
      </div>
    </form>
  </div>
</div>

@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
  Swal.fire({
    icon: 'success',
    title: 'Éxito',
    text: @json(session('success')),
    confirmButtonColor: '#3085d6'
  });
</script>
@endif

@if(session('error'))
<script>
  Swal.fire({
    icon: 'error',
    title: 'Error',
    text: @json(session('error')),
    confirmButtonColor: '#d33'
  });
</script>
@endif

<script>
  // Confirmación para toggle
  document.querySelectorAll('.toggle-form').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const isActive = this.querySelector('button').title.includes('Desactivar');
      const action = isActive ? 'desactivar' : 'activar';

      Swal.fire({
        title: `¿${action.charAt(0).toUpperCase() + action.slice(1)} este rol?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          this.submit();
        }
      });
    });
  });
</script>
@stop