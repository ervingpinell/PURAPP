@extends('adminlte::page')

@section('title', 'Reservas')

@section('content_header')
    <h1>Gestión de Roles</h1>
@stop

@section('css')
<style>
  .modal-top .modal-dialog { margin: 5.5vh auto 1.5rem !important; }
  .modal-elevated .modal-dialog { margin: 6vh auto 1.5rem !important; }
  .btn-toggle { min-width: 38px; }
  .table td.text-end, .table th.text-end { text-align: end; }
</style>
@endsection

@section('content')

    <!-- Botón para crear nuevo rol -->
    <div class="mb-3">
        <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearRol">
            <i class="fas fa-plus"></i> Añadir Rol
        </a>
    </div>

    @php
      $curStatus   = $status ?? request('status', 'all');
      $isSortingById = ($sort ?? request('sort', 'name')) === 'id';
      $currentDir    = ($dir ?? request('dir', 'asc'));
      $nextDir       = $currentDir === 'asc' ? 'desc' : 'asc';
      $iconDir       = $currentDir === 'asc' ? 'fa-sort-numeric-down' : 'fa-sort-numeric-up-alt';
      $urlSortId     = route('admin.roles.index', [
          'q'      => $q ?? request('q'),
          'status' => $curStatus,
          'sort'   => 'id',
          'dir'    => $isSortingById ? $nextDir : 'asc',
      ]);
      $urlClear = route('admin.roles.index');
    @endphp

    {{-- Barra de búsqueda, filtro por estado y acciones --}}
    <form method="GET" action="{{ route('admin.roles.index') }}" class="mb-3">
      <div class="row g-2 align-items-end">

        {{-- Buscar por nombre + lupa alineada --}}
        <div class="col-lg-5">
          <label for="q" class="form-label mb-1">Buscar por nombre</label>
          <div class="input-group">
            <input type="text" name="q" id="q" class="form-control"
                   placeholder="Ej: Administrador"
                   value="{{ old('q', $q ?? request('q')) }}">
            <button type="submit" class="btn btn-primary" title="Buscar">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </div>

        {{-- Estado --}}
        <div class="col-lg-3">
          <label for="status" class="form-label mb-1">Estado</label>
          <select name="status" id="status" class="form-select">
            <option value="all"      {{ $curStatus === 'all' ? 'selected' : '' }}>Todos</option>
            <option value="active"   {{ $curStatus === 'active' ? 'selected' : '' }}>Activos</option>
            <option value="inactive" {{ $curStatus === 'inactive' ? 'selected' : '' }}>Inactivos</option>
          </select>
        </div>

        {{-- Preservar sort/dir actuales --}}
        <input type="hidden" name="sort" value="{{ $sort ?? request('sort', 'name') }}">
        <input type="hidden" name="dir"  value="{{ $dir  ?? request('dir',  'asc')  }}">

        {{-- Acciones a la derecha: Ordenar por ID + Limpiar --}}
        <div class="col-lg-4 d-grid d-lg-flex justify-content-lg-end gap-2">
          <a href="{{ $urlSortId }}" class="btn btn-secondary" title="Ordenar por ID">
            <i class="fas {{ $isSortingById ? $iconDir : 'fa-sort' }}"></i>
            Ordenar por ID
            @if($isSortingById)
              <small class="text-muted">({{ strtoupper($currentDir) }})</small>
            @endif
          </a>
          <a href="{{ $urlClear }}" class="btn btn-outline-secondary" title="Limpiar filtros">
            <i class="fas fa-eraser"></i> Limpiar
          </a>
        </div>

      </div>
    </form>

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Rol</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td>{{ $role->role_id }}</td>
                            <td>{{ $role->role_name }}</td>
                            <td>{{ $role->description ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $role->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $role->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-end">

                                {{-- Editar (abre modal) --}}
                                <button type="button"
                                        class="btn btn-sm btn-edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditarRol{{ $role->role_id }}"
                                        title="Editar">
                                  <i class="fas fa-edit"></i>
                                </button>

                                {{-- Toggle (activar/desactivar) --}}
                                <form action="{{ route('admin.roles.toggle', $role->role_id) }}"
                                      method="POST"
                                      class="d-inline toggle-form">
                                    @csrf
                                    @method('PATCH')

                                    {{-- preserva filtros al volver --}}
                                    <input type="hidden" name="q"      value="{{ $q ?? request('q') }}">
                                    <input type="hidden" name="status" value="{{ $curStatus }}">
                                    <input type="hidden" name="sort"   value="{{ $sort ?? request('sort','name') }}">
                                    <input type="hidden" name="dir"    value="{{ $dir ?? request('dir','asc') }}">

                                    <button type="submit"
                                            class="btn btn-sm {{ $role->is_active ? 'btn-toggle' : 'btn-secondary' }} btn-toggle"
                                            title="{{ $role->is_active ? 'Desactivar' : 'Activar' }}">
                                        <i class="fas {{ $role->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                    </button>
                                </form>

                                {{-- Eliminar (comentado)
                                <form action="{{ route('admin.roles.destroy', $role->role_id) }}"
                                      method="POST"
                                      class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger btn-delete" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                --}}
                            </td>
                        </tr>

                        {{-- Modal Editar Rol (subido con .modal-top) --}}
                        <div class="modal fade modal-top" id="modalEditarRol{{ $role->role_id }}" tabindex="-1" aria-labelledby="modalEditarRolLabel{{ $role->role_id }}" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <form action="{{ route('admin.roles.update', $role->role_id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="modal-header">
                                  <h5 class="modal-title" id="modalEditarRolLabel{{ $role->role_id }}">Editar Rol</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>

                                <div class="modal-body">
                                  <div class="mb-3">
                                    <label for="role_name_{{ $role->role_id }}" class="form-label">Nombre del Rol</label>
                                    <input type="text"
                                           name="role_name"
                                           id="role_name_{{ $role->role_id }}"
                                           class="form-control @error('role_name') is-invalid @enderror"
                                           value="{{ old('role_name', $role->role_name) }}"
                                           required>
                                    @error('role_name')
                                      <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                  </div>

                                  <div class="mb-3">
                                    <label for="description_{{ $role->role_id }}" class="form-label">Descripción</label>
                                    <textarea name="description"
                                              id="description_{{ $role->role_id }}"
                                              class="form-control @error('description') is-invalid @enderror"
                                              rows="3">{{ old('description', $role->description) }}</textarea>
                                    @error('description')
                                      <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                  </div>
                                </div>

                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                  <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No hay roles registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Crear Rol -->
    <div class="modal fade modal-elevated" id="modalCrearRol" tabindex="-1" aria-labelledby="modalCrearRolLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.roles.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCrearRolLabel">Crear Nuevo Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="role_name" class="form-label">Nombre del Rol</label>
                        <input type="text" name="role_name" id="role_name" class="form-control" required maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción (opcional)</label>
                        <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>

@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success'))
        <script>
            Swal.fire({ icon: 'success', title: 'Éxito', text: @json(session('success')), confirmButtonColor: '#3085d6' });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({ icon: 'error', title: 'Error', text: @json(session('error')), confirmButtonColor: '#d33' });
        </script>
    @endif

    <script>
        // Confirmación toggle
        document.querySelectorAll('.toggle-form .btn-toggle').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const form = this.closest('form');
                const isDeactivate = this.classList.contains('btn-danger');
                const actionWord = isDeactivate ? 'desactivar' : 'activar';

                Swal.fire({
                    title: `${actionWord.charAt(0).toUpperCase() + actionWord.slice(1)} rol`,
                    text: `¿Seguro que deseas ${actionWord} este rol?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, continuar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
@stop
