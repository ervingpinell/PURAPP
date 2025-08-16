{{-- resources/views/admin/policies/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Categorías de Políticas')

@section('content_header')
  <h1 class="mb-2">
    <i class="fas fa-shield-alt"></i> Categorías de Políticas
  </h1>
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

  <div class="mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPolicyModal">
      <i class="fas fa-plus"></i> Nueva categoría
    </button>
  </div>

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead class="table-dark">
            <tr class="text-center">
              <th>ID</th>
              <th>Nombre interno</th>
              <th>Título ({{ strtoupper(app()->getLocale()) }})</th>
              <th>Rango de vigencia</th>
              <th>Estado</th>
              <th>Secciones</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($policies as $p)
              @php
                $t = $p->translation();
                $from = $p->effective_from
                    ? \Illuminate\Support\Carbon::parse($p->effective_from)->format('Y-m-d')
                    : null;
                $to = $p->effective_to
                    ? \Illuminate\Support\Carbon::parse($p->effective_to)->format('Y-m-d')
                    : null;
              @endphp
              <tr class="text-center">
                <td>{{ $p->policy_id }}</td>
                <td class="text-start"><code>{{ $p->name }}</code></td>
                <td class="text-start">{{ $t?->title ?? '—' }}</td>
                <td>
                  @if($from || $to)
                    {{ $from ?? '—' }} &rarr; {{ $to ?? '—' }}
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td>
                  <span class="badge {{ $p->is_active ? 'bg-success' : 'bg-danger' }}">
                    <i class="fas {{ $p->is_active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                    {{ $p->is_active ? 'Activa' : 'Inactiva' }}
                  </span>
                </td>
                <td>{{ $p->sections_count ?? $p->sections()->count() }}</td>
                <td>
                  <div class="actions text-center my-1">
                    {{-- 👁️ Ver (igual que Carritos) --}}
                    <a class="btn btn-info btn-sm me-1"
                       href="{{ route('admin.policies.sections.index', $p) }}"
                       title="Ver secciones" data-bs-toggle="tooltip">
                      <i class="fas fa-eye"></i>
                    </a>

                    <button class="btn btn-edit btn-sm me-1"
                            data-bs-toggle="modal"
                            data-bs-target="#editPolicyModal-{{ $p->policy_id }}"
                            title="Editar" data-bs-toggle="tooltip">
                      <i class="fas fa-edit"></i>
                    </button>

                    <form class="d-inline me-1" method="POST" action="{{ route('admin.policies.toggle', $p) }}">
                      @csrf
                      <button class="btn btn-sm {{ $p->is_active ? 'btn-toggle' : 'btn-toggle' }}"
                              title="{{ $p->is_active ? 'Desactivar categoría' : 'Activar categoría' }}"
                              data-bs-toggle="tooltip">
                        <i class="fas {{ $p->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                      </button>
                    </form>

                    <form class="d-inline" method="POST"
                          action="{{ route('admin.policies.destroy', $p) }}"
                          onsubmit="return confirm('¿Eliminar la categoría y TODAS sus secciones?');">
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
              <tr><td colspan="7" class="text-center text-muted p-4">Sin categorías registradas.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- MODAL: Nueva categoría --}}
  <div class="modal fade" id="createPolicyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <form class="modal-content" method="POST" action="{{ route('admin.policies.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Nueva categoría</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nombre interno</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Vigencia desde</label>
              <input type="date" name="effective_from" class="form-control" value="{{ now()->toDateString() }}">
            </div>
            <div class="col-md-3">
              <label class="form-label">Vigencia hasta</label>
              <input type="date" name="effective_to" class="form-control">
            </div>
            <div class="col-md-3">
              <div class="form-check mt-4">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                       class="form-check-input" id="p-active-new" checked>
                <label class="form-check-label" for="p-active-new">Activa</label>
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
            <label class="form-label">Descripción ({{ strtoupper(app()->getLocale()) }})</label>
            <textarea name="content" class="form-control" rows="8" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </form>
    </div>
  </div>

  {{-- MODALES: Editar categoría --}}
  @foreach ($policies as $p)
    @php
      $tt = $p->translation();
      $fromVal = $p->effective_from
          ? \Illuminate\Support\Carbon::parse($p->effective_from)->format('Y-m-d')
          : '';
      $toVal = $p->effective_to
          ? \Illuminate\Support\Carbon::parse($p->effective_to)->format('Y-m-d')
          : '';
    @endphp
    <div class="modal fade" id="editPolicyModal-{{ $p->policy_id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form class="modal-content" method="POST" action="{{ route('admin.policies.update', $p) }}">
          @csrf @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title">Editar categoría</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Nombre interno</label>
                <input type="text" name="name" class="form-control" value="{{ $p->name }}" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">Vigencia desde</label>
                <input type="date" name="effective_from" class="form-control" value="{{ $fromVal }}">
              </div>
              <div class="col-md-3">
                <label class="form-label">Vigencia hasta</label>
                <input type="date" name="effective_to" class="form-control" value="{{ $toVal }}">
              </div>
              <div class="col-md-3">
                <div class="form-check mt-4">
                  <input type="hidden" name="is_active" value="0">
                  <input type="checkbox" name="is_active" value="1"
                         class="form-check-input"
                         id="p-active-{{ $p->policy_id }}" {{ $p->is_active ? 'checked' : '' }}>
                  <label class="form-check-label" for="p-active-{{ $p->policy_id }}">Activa</label>
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
              <label class="form-label">Descripción ({{ strtoupper(app()->getLocale()) }})</label>
              <textarea name="content" class="form-control" rows="8">{{ $tt?->content }}</textarea>
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
