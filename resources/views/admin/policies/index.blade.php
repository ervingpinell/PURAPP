{{-- resources/views/admin/policies/index.blade.php --}}
@extends('adminlte::page')

@section('title', __('m_config.policies.categories_title'))

@section('content_header')
  <h1 class="mb-2">
    <i class="fas fa-shield-alt"></i> {{ __('m_config.policies.categories_title') }}
  </h1>
@stop

@section('content')
  {{-- ALERTAS (fallback si no hay JS) --}}
  <noscript>
    @if (session('success'))
      <div class="alert alert-success">{{ __(session('success')) }}</div>
    @endif
    @if (session('error'))
      <div class="alert alert-danger">{{ __(session('error')) }}</div>
    @endif
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
      </div>
    @endif
  </noscript>

  <div class="mb-3 d-flex gap-2">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPolicyModal">
      <i class="fas fa-plus"></i> {{ __('m_config.policies.new_category') }}
    </button>
  </div>

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead class="table-dark">
            <tr class="text-center">
              <th>{{ __('m_config.policies.id') }}</th>
              <th class="text-center">{{ __('m_config.policies.title_current_locale') }}</th>
              <th class="text-center">{{ __('m_config.policies.slug') }}</th>
              <th>{{ __('m_config.policies.validity_range') }}</th>
              <th>{{ __('m_config.policies.status') }}</th>
              <th>{{ __('m_config.policies.sections') }}</th>
              <th>{{ __('m_config.policies.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($policies as $p)
              @php
                $t    = $p->translation(); // traducción del locale actual
                $from = $p->effective_from ? \Illuminate\Support\Carbon::parse($p->effective_from)->format('Y-m-d') : null;
                $to   = $p->effective_to   ? \Illuminate\Support\Carbon::parse($p->effective_to)->format('Y-m-d')   : null;
              @endphp
              <tr class="text-center">
                <td>{{ $p->policy_id }}</td>
                <td class="text-center">{{ $t?->name ?? '—' }}</td>
                <td class="text-center">
                  <code class="text-muted">{{ $p->slug }}</code>
                </td>
                <td>
                  @if($from || $to)
                    {{ $from ?? '—' }} &rarr; {{ $to ?? '—' }}
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td>
                  <span class="badge {{ $p->is_active ? 'bg-success' : 'bg-secondary' }}">
                    <i class="fas {{ $p->is_active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                    {{ $p->is_active ? __('m_config.policies.active') : __('m_config.policies.inactive') }}
                  </span>
                </td>
                <td>{{ $p->sections_count ?? $p->sections()->count() }}</td>
                <td>
                  <div class="actions text-center my-1">
                    <a class="btn btn-info btn-sm me-1"
                       href="{{ route('admin.policies.sections.index', $p) }}"
                       title="{{ __('m_config.policies.view_sections') }}" data-bs-toggle="tooltip">
                      <i class="fas fa-eye"></i>
                    </a>

                    <button class="btn btn-edit btn-sm me-1"
                            data-bs-toggle="modal"
                            data-bs-target="#editPolicyModal-{{ $p->policy_id }}"
                            title="{{ __('m_config.policies.edit') }}">
                      <i class="fas fa-edit"></i>
                    </button>

                    {{-- Toggle con SweetAlert --}}
                    <form class="d-inline me-1 js-confirm-toggle"
                          method="POST"
                          action="{{ route('admin.policies.toggle', $p) }}"
                          data-active="{{ $p->is_active ? 1 : 0 }}">
                      @csrf
                      <button class="btn {{ $p->is_active ? 'btn-toggle' : 'btn-secondary' }} btn-sm"
                              title="{{ $p->is_active ? __('m_config.policies.deactivate_category') : __('m_config.policies.activate_category') }}"
                              data-bs-toggle="tooltip">
                        <i class="fas {{ $p->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                      </button>
                    </form>

                    {{-- Eliminar con SweetAlert --}}
                    <form class="d-inline js-confirm-delete"
                          method="POST"
                          action="{{ route('admin.policies.destroy', $p) }}"
                          data-message="{{ __('m_config.policies.delete_category_confirm') }}">
                      @csrf @method('DELETE')
                      <button class="btn btn-delete btn-sm"
                              title="{{ __('m_config.policies.delete') }}" data-bs-toggle="tooltip">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr><td colspan="7" class="text-center text-muted p-4">{{ __('m_config.policies.no_categories') }}</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- MODAL: Nueva categoría (base + traducción automática en store) --}}
  <div class="modal fade" id="createPolicyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <form class="modal-content" method="POST" action="{{ route('admin.policies.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">{{ __('m_config.policies.new_category') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('m_config.policies.close') }}"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">{{ __('m_config.policies.valid_from') }}</label>
              <input type="date" name="effective_from" class="form-control" value="{{ now()->toDateString() }}">
            </div>
            <div class="col-md-3">
              <label class="form-label">{{ __('m_config.policies.valid_to') }}</label>
              <input type="date" name="effective_to" class="form-control">
            </div>
            <div class="col-md-3 d-flex align-items-end">
              <div class="form-check">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                       class="form-check-input" id="p-active-new" checked>
                <label class="form-check-label" for="p-active-new">{{ __('m_config.policies.active') }}</label>
              </div>
            </div>
          </div>

          <hr>

          <div class="mb-3">
            <label class="form-label">{{ __('m_config.policies.name') }}</label>
            <input type="text" name="name" class="form-control" required>
            <small class="text-muted">{{ __('m_config.policies.lang_autodetect_hint') }}</small>
          </div>

          <div class="mb-3">
            <label class="form-label">
              {{ __('m_config.policies.slug') }}
              <span class="text-muted small">({{ __('m_config.policies.slug_hint') }})</span>
            </label>
            <input type="text" name="slug" class="form-control" placeholder="se-genera-automaticamente">
            <small class="text-muted">{{ __('m_config.policies.slug_auto_hint') }}</small>
          </div>

          <div class="mb-3">
            <label class="form-label">{{ __('m_config.policies.description_label') }}</label>
            <textarea name="content" class="form-control" rows="8" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary"><i class="fas fa-save"></i> {{ __('m_config.policies.register') }}</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_config.policies.close') }}</button>
        </div>
      </form>
    </div>
  </div>

  {{-- MODALES: Editar categoría (actualiza base + traducción del locale actual) --}}
  @foreach ($policies as $p)
    @php
      $fromVal = $p->effective_from ? \Illuminate\Support\Carbon::parse($p->effective_from)->format('Y-m-d') : '';
      $toVal   = $p->effective_to   ? \Illuminate\Support\Carbon::parse($p->effective_to)->format('Y-m-d')   : '';
      $t       = $p->translation(); // traducción del locale actual
    @endphp
    <div class="modal fade" id="editPolicyModal-{{ $p->policy_id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form class="modal-content" method="POST" action="{{ route('admin.policies.update', $p) }}">
          @csrf @method('PUT')

          {{-- IMPORTANTE: locale a editar (por defecto, el actual) --}}
          <input type="hidden" name="locale" value="{{ app()->getLocale() }}">

          <div class="modal-header">
            <h5 class="modal-title">{{ __('m_config.policies.edit_category') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('m_config.policies.close') }}"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label">{{ __('m_config.policies.valid_from') }}</label>
                <input type="date" name="effective_from" class="form-control" value="{{ $fromVal }}">
              </div>
              <div class="col-md-3">
                <label class="form-label">{{ __('m_config.policies.valid_to') }}</label>
                <input type="date" name="effective_to" class="form-control" value="{{ $toVal }}">
              </div>
              <div class="col-md-3">
                <div class="form-check mt-4">
                  <input type="hidden" name="is_active" value="0">
                  <input type="checkbox" name="is_active" value="1"
                         class="form-check-input"
                         id="p-active-{{ $p->policy_id }}" {{ $p->is_active ? 'checked' : '' }}>
                  <label class="form-check-label" for="p-active-{{ $p->policy_id }}">{{ __('m_config.policies.active') }}</label>
                </div>
              </div>
            </div>

            <hr>

            {{-- Título (afecta base y la traducción del locale actual) --}}
            <div class="mb-3">
              <label class="form-label">{{ __('m_config.policies.name') }}</label>
              <input type="text" name="name" class="form-control"
                     value="{{ old('name', $t?->name ?? $p->name) }}">
            </div>

            {{-- SLUG editable --}}
            <div class="mb-3">
              <label class="form-label">
                {{ __('m_config.policies.slug') }}
                <span class="text-muted small">({{ __('m_config.policies.slug_hint') }})</span>
              </label>
              <input type="text" name="slug" class="form-control"
                     value="{{ old('slug', $p->slug) }}"
                     placeholder="terminos-y-condiciones">
              <small class="text-muted">
                <i class="fas fa-info-circle"></i> {{ __('m_config.policies.slug_edit_hint') }}
              </small>
            </div>

            {{-- CONTENIDO traducido del locale actual --}}
            <div class="mb-3">
              <label class="form-label">{{ __('m_config.policies.description_label') }}</label>
              <textarea name="content" class="form-control" rows="8">{{ old('content', $t?->content) }}</textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary"><i class="fas fa-save"></i> {{ __('m_config.policies.save_changes') }}</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_config.policies.close') }}</button>
          </div>
        </form>
      </div>
    </div>
  @endforeach
@stop

@push('css')
<style>
  .btn-edit { background:#0dcaf0; color:#000; }
  .btn-edit:hover { filter: brightness(.95); }
  .btn-toggle { background:#198754; color:#fff; }
  .btn-delete { background:#dc3545; color:#fff; }
  .btn-delete:hover { filter: brightness(.95); }
</style>
@endpush

@section('js')
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  {{-- Éxito / Error (usa claves m_config.policies.* si las mandas como clave en session) --}}
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
        title: @json(__('m_config.policies.error_title')),
        text: @json(__(session('error')))
      });
    </script>
  @endif

  <script>
  document.addEventListener('DOMContentLoaded', () => {
    // Tooltips
    [...document.querySelectorAll('[data-bs-toggle="tooltip"]')].forEach(el => new bootstrap.Tooltip(el));

    // Limpiar backdrops duplicados
    document.addEventListener('hidden.bs.modal', () => {
      const backs = document.querySelectorAll('.modal-backdrop');
      if (backs.length > 1) backs.forEach((b,i) => { if (i < backs.length-1) b.remove(); });
    });

    // Validaciones (errores) como modal
    const valErrors = @json($errors->any() ? $errors->all() : []);
    if (valErrors && valErrors.length) {
      const list = '<ul class="text-start mb-0">' + valErrors.map(e => `<li>${e}</li>`).join('') + '</ul>';
      Swal.fire({
        icon: 'warning',
        title: @json(__('m_config.policies.validation_errors')),
        html: list,
        confirmButtonText: @json(__('m_config.policies.ok')),
      });
    }

    // Confirmación ELIMINAR categoría
    document.querySelectorAll('.js-confirm-delete').forEach(form => {
      form.addEventListener('submit', (ev) => {
        ev.preventDefault();
        const msg = form.dataset.message || @json(__('m_config.policies.delete_category_confirm'));
        Swal.fire({
          title: msg,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#6c757d',
          confirmButtonText: @json(__('m_config.policies.delete')),
          cancelButtonText: @json(__('m_config.policies.cancel')),
        }).then(res => { if (res.isConfirmed) form.submit(); });
      });
    });

    // Confirmación ACTIVAR / DESACTIVAR categoría
    document.querySelectorAll('.js-confirm-toggle').forEach(form => {
      form.addEventListener('submit', (ev) => {
        ev.preventDefault();
        const isActive = form.dataset.active === '1';
        const titleVar = isActive ? @json(__('m_config.policies.deactivate_category')) : @json(__('m_config.policies.activate_category'));
        Swal.fire({
          title: titleVar + '?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: isActive ? '#d33' : '#28a745',
          cancelButtonColor: '#6c757d',
          confirmButtonText: titleVar,
          cancelButtonText: @json(__('m_config.policies.cancel')),
        }).then(res => { if (res.isConfirmed) form.submit(); });
      });
    });
  });
  </script>
@stop
