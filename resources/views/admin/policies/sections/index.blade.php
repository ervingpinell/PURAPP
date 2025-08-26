@extends('adminlte::page')

@section('title', __('policies.sections_title', ['policy' => ($policy->translation()?->name ?? $policy->name)]))

@section('content_header')
  <div class="d-flex align-items-center justify-content-between flex-wrap">
    <h1 class="mb-2">
      <i class="fas fa-list-ul"></i>
      {{ __('policies.sections_title', ['policy' => ($policy->translation()?->name ?? $policy->name)]) }}
    </h1>
    <div class="mb-2">
      <a class="btn btn-secondary" href="{{ route('admin.policies.index') }}">
        <i class="fas fa-arrow-left"></i> {{ __('policies.back_to_categories') }}
      </a>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSectionModal">
        <i class="fas fa-plus"></i> {{ __('policies.new_section') }}
      </button>
    </div>
  </div>
@stop

@section('content')
  {{-- Fallback solo si no hay JS --}}
  <noscript>
    @if(session('success'))
      <div class="alert alert-success">{{ __(session('success')) }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">{{ __(session('error')) }}</div>
    @endif
    @if ($errors->any())
      <div class="alert alert-warning">
        <strong>{{ __('policies.validation_errors') ?? 'Revisa los campos.' }}</strong>
        <ul class="mb-0 mt-1">
          @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    @endif
  </noscript>

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead class="table-dark">
            <tr class="text-center">
              <th>{{ __('policies.id') }}</th>
              <th class="text-center">{{ __('policies.name') }}</th>
              <th>{{ __('policies.order') }}</th>
              <th>{{ __('policies.status') }}</th>
              <th style="width: 280px;">{{ __('policies.actions') }}</th>
            </tr>
          </thead>
          <tbody>
          @forelse ($sections as $s)
            <tr>
              <td class="text-center">{{ $s->section_id }}</td>
              <td class="text-center text-break">{{ $s->name ?? '—' }}</td>
              <td class="text-center">{{ $s->sort_order }}</td>
              <td class="text-center">
                @if($s->is_active)
                  <span class="badge bg-success">{{ __('policies.active') }}</span>
                @else
                  <span class="badge bg-secondary">{{ __('policies.inactive') }}</span>
                @endif
              </td>
              <td class="text-center">
                <div class="d-flex justify-content-center gap-2 flex-wrap">

                  {{-- Editar (solo base: name/sort_order/is_active) --}}
                  <button class="btn btn-sm btn-edit"
                          title="{{ __('policies.edit') }}"
                          data-bs-toggle="modal"
                          data-bs-target="#editSectionModal-{{ $s->section_id }}">
                    <i class="fas fa-edit"></i>
                  </button>

                  {{-- Toggle con SweetAlert --}}
                  <form action="{{ route('admin.policies.sections.toggle', [$policy, $s]) }}" method="POST"
                        class="d-inline js-confirm-toggle" data-active="{{ $s->is_active ? 1 : 0 }}">
                    @csrf
                    <button class="btn btn-sm {{ $s->is_active ? 'btn-toggle' : 'btn-secondary' }}"
                      title="{{ $s->is_active ? __('policies.deactivate_section') : __('policies.activate_section') }}"
                      data-bs-toggle="tooltip">
                      <i class="fas {{ $s->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                    </button>
                  </form>

                  {{-- Eliminar con SweetAlert --}}
                  <form action="{{ route('admin.policies.sections.destroy', [$policy, $s]) }}" method="POST"
                        class="d-inline js-confirm-delete"
                        data-message="{{ __('policies.confirm_delete_section') }}">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-delete"
                      title="{{ __('policies.delete') }}" data-bs-toggle="tooltip">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>

                </div>
              </td>
            </tr>

            {{-- Modal Edit (solo base) --}}
            <div class="modal fade" id="editSectionModal-{{ $s->section_id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                  <form action="{{ route('admin.policies.sections.update', [$policy, $s]) }}" method="POST" class="js-confirm-edit">
                    @csrf @method('PUT')
                    <div class="modal-header">
                      <h5 class="modal-title">{{ __('policies.edit_section') }}</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <div class="row g-3">
                        <div class="col-md-6">
                          <label class="form-label">{{ __('policies.name') }}</label>
                          <input type="text" name="name" class="form-control" value="{{ $s->name }}">
                        </div>

                        <div class="col-md-3">
                          <label class="form-label">{{ __('policies.order') }}</label>
                          <input type="number" min="0" name="sort_order" class="form-control" value="{{ $s->sort_order }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                          <div class="form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" id="is_active_{{ $s->section_id }}" class="form-check-input" {{ $s->is_active ? 'checked' : '' }}>
                            <label for="is_active_{{ $s->section_id }}" class="form-check-label">{{ __('policies.active') }}</label>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button class="btn btn-primary">{{ __('policies.save') }}</button>
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('policies.close') }}</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

          @empty
            <tr><td colspan="5" class="text-center text-muted">{{ __('policies.no_sections') }}</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Modal Create (name/content base + orden/activo) --}}
  <div class="modal fade" id="createSectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <form action="{{ route('admin.policies.sections.store', $policy) }}" method="POST" class="js-confirm-create">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">{{ __('policies.new_section') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label">{{ __('policies.order') }}</label>
                <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
              </div>
              <div class="col-md-3 d-flex align-items-end">
                <div class="form-check">
                  <input type="hidden" name="is_active" value="0">
                  <input type="checkbox" name="is_active" value="1"
                         id="is_active_new" class="form-check-input" {{ old('is_active', 1) ? 'checked' : '' }}>
                  <label for="is_active_new" class="form-check-label">{{ __('policies.active') }}</label>
                </div>
              </div>

              <hr class="my-2">

              <div class="col-md-6">
                <label class="form-label">{{ __('policies.name') }}</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                <small class="text-muted">{{ __('policies.lang_autodetect_hint') ?? 'Puedes escribir en cualquier idioma; se detecta automáticamente.' }}</small>
              </div>
              <div class="col-12">
                <label class="form-label">{{ __('policies.translation_content') }}</label>
                <textarea name="content" class="form-control" rows="10" required>{{ old('content') }}</textarea>
              </div>

            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary">{{ __('policies.save') }}</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('policies.close') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@stop

@section('js')
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  {{-- Éxito / Error (sin toast, estilo carrito) --}}
  @if(session('success'))
    <script>
      Swal.fire({
        icon: 'success',
        title: @json(__(session('success'))),
        showConfirmButton: false,
        timer: 2000
      });
    </script>
  @endif

  @if(session('error'))
    <script>
      Swal.fire({
        icon: 'error',
        title: @json(__('policies.error_title') ?? 'Error'),
        text: @json(__(session('error')))
      });
    </script>
  @endif

  <script>
  document.addEventListener('DOMContentLoaded', () => {
    // tooltips
    [...document.querySelectorAll('[data-bs-toggle="tooltip"]')].forEach(el => new bootstrap.Tooltip(el));

    // limpiar backdrops duplicados
    document.addEventListener('hidden.bs.modal', () => {
      const backs = document.querySelectorAll('.modal-backdrop');
      if (backs.length > 1) backs.forEach((b,i) => { if (i < backs.length-1) b.remove(); });
    });

    // Validaciones como modal
    const valErrors = @json($errors->any() ? $errors->all() : []);
    if (valErrors && valErrors.length) {
      const list = '<ul class="text-start mb-0">' + valErrors.map(e => `<li>${e}</li>`).join('') + '</ul>';
      Swal.fire({
        icon: 'warning',
        title: @json(__('policies.validation_errors') ?? 'Hay errores de validación'),
        html: list,
        confirmButtonText: @json(__('policies.ok') ?? 'Aceptar'),
      });
    }

    // Confirmación CREAR sección
    document.querySelectorAll('.js-confirm-create').forEach(form => {
      form.addEventListener('submit', (ev) => {
        ev.preventDefault();
        Swal.fire({
          title: @json(__('policies.confirm_create_section') ?? '¿Crear esta sección?'),
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#28a745',
          cancelButtonColor: '#6c757d',
          confirmButtonText: @json(__('policies.create') ?? 'Crear'),
          cancelButtonText: @json(__('policies.cancel') ?? 'Cancelar'),
        }).then((res) => { if (res.isConfirmed) form.submit(); });
      });
    });

    // Confirmación EDITAR sección
    document.querySelectorAll('.js-confirm-edit').forEach(form => {
      form.addEventListener('submit', (ev) => {
        ev.preventDefault();
        Swal.fire({
          title: @json(__('policies.confirm_edit_section') ?? '¿Guardar cambios de la sección?'),
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#0d6efd',
          cancelButtonColor: '#6c757d',
          confirmButtonText: @json(__('policies.save_changes') ?? 'Guardar cambios'),
          cancelButtonText: @json(__('policies.cancel') ?? 'Cancelar'),
        }).then((res) => { if (res.isConfirmed) form.submit(); });
      });
    });

    // Confirmación ELIMINAR sección
    document.querySelectorAll('.js-confirm-delete').forEach(form => {
      form.addEventListener('submit', (ev) => {
        ev.preventDefault();
        const msg = form.dataset.message || @json(__('policies.confirm_delete_section'));
        Swal.fire({
          title: msg,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#6c757d',
          confirmButtonText: @json(__('policies.delete') ?? 'Eliminar'),
          cancelButtonText: @json(__('policies.cancel') ?? 'Cancelar'),
        }).then((res) => { if (res.isConfirmed) form.submit(); });
      });
    });

    // Confirmación ACTIVAR/DESACTIVAR sección
    document.querySelectorAll('.js-confirm-toggle').forEach(form => {
      form.addEventListener('submit', (ev) => {
        ev.preventDefault();
        const isActive = form.dataset.active === '1';
        Swal.fire({
          title: isActive
            ? @json(__('policies.confirm_deactivate_section'))
            : @json(__('policies.confirm_activate_section')),
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: isActive ? '#d33' : '#28a745',
          cancelButtonColor: '#6c757d',
          confirmButtonText: isActive
            ? @json(__('policies.deactivate') ?? 'Desactivar')
            : @json(__('policies.activate') ?? 'Activar'),
          cancelButtonText: @json(__('policies.cancel') ?? 'Cancelar'),
        }).then((res) => { if (res.isConfirmed) form.submit(); });
      });
    });
  });
  </script>
@stop
