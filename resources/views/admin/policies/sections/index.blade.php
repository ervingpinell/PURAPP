{{-- resources/views/admin/policies/sections/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Secciones — ' . ($policy->translation()?->title ?? $policy->name))

@section('content_header')
  <div class="d-flex align-items-center justify-content-between flex-wrap">
    <h1 class="mb-2">
      <i class="fas fa-list-ul"></i>
      Secciones — <small class="text-muted">{{ $policy->translation()?->title ?? $policy->name }}</small>
    </h1>
    <div class="mb-2">
      <a class="btn btn-secondary" href="{{ route('admin.policies.index') }}">
        <i class="fas fa-arrow-left"></i> Volver a categorías
      </a>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSectionModal">
        <i class="fas fa-plus"></i> Nueva sección
      </button>
    </div>
  </div>
@stop

@section('content')
  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
    </div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead class="table-dark">
            <tr class="text-center">
              <th>ID</th>
              <th>Clave</th>
              <th>Orden</th>
              <th>Título ({{ strtoupper(app()->getLocale()) }})</th>
              <th>Estado</th>
              <th style="width: 240px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($sections as $s)
              @php $t = $s->translation(); @endphp
              <tr class="text-center">
                <td>{{ $s->section_id }}</td>
                <td><code>{{ $s->key ?? '—' }}</code></td>
                <td>{{ $s->sort_order }}</td>
                <td class="text-start">{{ $t?->title ?? '—' }}</td>
                <td>
                  <span class="badge {{ $s->is_active ? 'bg-success' : 'bg-danger' }}">
                    <i class="fas {{ $s->is_active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                    {{ $s->is_active ? 'Activa' : 'Inactiva' }}
                  </span>
                </td>
                <td>
                  <div class="actions text-center my-1">
                    <button class="btn btn-edit btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#editSectionModal-{{ $s->section_id }}"
                            title="Editar" data-bs-toggle="tooltip">
                      <i class="fas fa-edit"></i>
                    </button>

                    <form class="d-inline" method="POST"
                          action="{{ route('admin.policies.sections.toggle', [$policy, $s]) }}">
                      @csrf
                      <button class="btn btn-sm {{ $s->is_active ? 'btn-toggle' : 'btn-toggle' }}"
                              title="{{ $s->is_active ? 'Desactivar sección' : 'Activar sección' }}"
                              data-bs-toggle="tooltip">
                        <i class="fas {{ $s->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                      </button>
                    </form>

                    <form class="d-inline" method="POST"
                          action="{{ route('admin.policies.sections.destroy', [$policy, $s]) }}"
                          onsubmit="return confirm('¿Eliminar esta sección?');">
                      @csrf @method('DELETE')
                      <button class="btn btn-danger btn-sm"
                              title="Eliminar" data-bs-toggle="tooltip">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center text-muted p-4">Sin secciones registradas.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- MODAL: Nueva sección --}}
  <div class="modal fade" id="createSectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <form class="modal-content" method="POST" action="{{ route('admin.policies.sections.store', $policy) }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Nueva sección</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Clave interna (opcional)</label>
              <input type="text" name="key" class="form-control" placeholder="p.ej. cookies">
            </div>
            <div class="col-md-3">
              <label class="form-label">Orden</label>
              <input type="number" min="0" name="sort_order" class="form-control" value="0">
            </div>
            <div class="col-md-3">
              <div class="form-check mt-4">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                       class="form-check-input" id="s-active-new" checked>
                <label class="form-check-label" for="s-active-new">Activa</label>
              </div>
            </div>
          </div>

          <hr>

          <input type="hidden" name="locale" value="{{ app()->getLocale() }}">

          <div class="mb-3">
            <label class="form-label">Título ({{ strtoupper(app()->getLocale()) }})</label>
            <input type="text" name="title" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Contenido ({{ strtoupper(app()->getLocale()) }})</label>
            <textarea name="content" class="form-control" rows="10" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </form>
    </div>
  </div>

  {{-- MODALES: Editar sección --}}
  @foreach ($sections as $s)
    @php $tt = $s->translation(); @endphp
    <div class="modal fade" id="editSectionModal-{{ $s->section_id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form class="modal-content" method="POST" action="{{ route('admin.policies.sections.update', [$policy, $s]) }}">
          @csrf @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title">Editar sección</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Clave interna (opcional)</label>
                <input type="text" name="key" class="form-control" value="{{ $s->key }}">
              </div>
              <div class="col-md-3">
                <label class="form-label">Orden</label>
                <input type="number" min="0" name="sort_order" class="form-control" value="{{ $s->sort_order }}">
              </div>
              <div class="col-md-3">
                <div class="form-check mt-4">
                  <input type="hidden" name="is_active" value="0">
                  <input type="checkbox" name="is_active" value="1"
                         class="form-check-input"
                         id="s-active-{{ $s->section_id }}" {{ $s->is_active ? 'checked' : '' }}>
                  <label class="form-check-label" for="s-active-{{ $s->section_id }}">Activa</label>
                </div>
              </div>
            </div>

            <hr>

            <input type="hidden" name="locale" value="{{ app()->getLocale() }}">
            <div class="mb-3">
              <label class="form-label">Título ({{ strtoupper(app()->getLocale()) }})</label>
              <input type="text" name="title" class="form-control" value="{{ $tt?->title }}">
            </div>
            <div class="mb-3">
              <label class="form-label">Contenido ({{ strtoupper(app()->getLocale()) }})</label>
              <textarea name="content" class="form-control" rows="10">{{ $tt?->content }}</textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary"><i class="fas fa-save"></i> Guardar cambios</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </form>
      </div>
    </div>
  @endforeach
@stop



@section('js')
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    [...document.querySelectorAll('[data-bs-toggle="tooltip"]')]
      .forEach(el => new bootstrap.Tooltip(el));
    document.addEventListener('hidden.bs.modal', () => {
      const backs = document.querySelectorAll('.modal-backdrop');
      if (backs.length > 1) backs.forEach((b,i) => { if (i < backs.length-1) b.remove(); });
    });
  });
  </script>
@stop
