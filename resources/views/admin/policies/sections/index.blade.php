{{-- resources/views/admin/policies/sections/index.blade.php --}}
@extends('adminlte::page')

@section('title', __('m_config.policies.sections_title', ['policy' => ($policy->name ?? '')]))

@section('content_header')
<div class="d-flex align-items-center justify-content-between flex-wrap">
  <h1 class="mb-2">
    <i class="fas fa-list-ul"></i>
    {{ __('m_config.policies.sections_title', ['policy' => ($policy->name ?? '')]) }}
    <small class="text-muted ms-2">({{ strtoupper(app()->getLocale()) }})</small>
  </h1>
  <div class="mb-2">
    <a class="btn btn-secondary" href="{{ route('admin.policies.index') }}">
      <i class="fas fa-arrow-left"></i> {{ __('m_config.policies.back_to_categories') }}
    </a>
    @can('create-policy-sections')
    <button class="btn btn-primary" data-toggle="modal" data-target="#createSectionModal">
      <i class="fas fa-plus"></i> {{ __('m_config.policies.new_section') }}
    </button>
    @endcan
  </div>
</div>
@stop

@section('content')
<noscript>
  @if(session('success'))
  <div class="alert alert-success">{{ __(session('success')) }}</div>
  @endif
  @if(session('error'))
  <div class="alert alert-danger">{{ __(session('error')) }}</div>
  @endif
  @if ($errors->any())
  <div class="alert alert-warning">
    <strong>{{ __('m_config.policies.validation_errors') ?? 'Revisa los campos.' }}</strong>
    <ul class="mb-0 mt-1">
      @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
  </div>
  @endif
</noscript>

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
  <div class="btn-group" role="group" aria-label="{{ __('m_config.policies.filter_status_aria') }}">
    <a href="{{ route('admin.policies.sections.index', ['policy' => $policy]) }}"
      class="btn btn-outline-primary {{ ($status ?? 'all') === 'all' ? 'active' : '' }}">
      {{ __('m_config.policies.filter_all') ?? 'Todas' }}
    </a>
    <a href="{{ route('admin.policies.sections.index', ['policy' => $policy, 'status' => 'archived']) }}"
      class="btn btn-outline-primary {{ ($status ?? '') === 'archived' ? 'active' : '' }}">
      {{ __('m_config.policies.filter_archived') }}
    </a>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle">
        <thead class="table-dark">
          <tr class="text-center">
            <th>{{ __('m_config.policies.id') }}</th>
            <th class="text-center">{{ __('m_config.policies.name') }}</th>
            <th>{{ __('m_config.policies.order') }}</th>
            @if(($status ?? 'active') === 'archived')
            <th>{{ __('m_config.policies.deleted_by') }}</th>
            <th>{{ __('m_config.policies.deleted_at') }}</th>
            @else
            <th>{{ __('m_config.policies.status') }}</th>
            @endif
            <th style="width: 280px;">{{ __('m_config.policies.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($sections as $s)
          @php
          $isTrashed = method_exists($s, 'trashed') && $s->trashed();
          @endphp
          <tr>
            <td class="text-center">{{ $s->section_id }}</td>
            <td class="text-center text-break">
              @if($isTrashed)
              <i class="fas fa-trash-alt text-muted me-1"></i>
              @endif
              {{ $s->name ?? '—' }}
            </td>
            <td class="text-center">{{ $s->sort_order }}</td>
            @if(($status ?? 'active') === 'archived')
            <td class="text-center">
              @if($s->deletedBy)
              <span class="badge bg-secondary">
                <i class="fas fa-user-times"></i> {{ $s->deletedBy->first_name }} {{ $s->deletedBy->last_name }}
              </span>
              @else
              <span class="text-muted">—</span>
              @endif
            </td>
            <td class="text-center">
              @if($s->deleted_at)
              {{ \Illuminate\Support\Carbon::parse($s->deleted_at)->format('d-M-Y H:i') }}
              @else
              <span class="text-muted">—</span>
              @endif
            </td>
            @else
            <td class="text-center">
              @if($s->is_active)
              <span class="badge bg-success">{{ __('m_config.policies.active') }}</span>
              @else
              <span class="badge bg-secondary">{{ __('m_config.policies.inactive') }}</span>
              @endif
            </td>
            @endif
            <td class="text-center">
              <div class="d-flex justify-content-center gap-2 flex-wrap">

                @if(!$isTrashed)
                {{-- Editar traducción del locale actual + base (orden/activo) --}}
                @can('edit-policy-sections')
                <button class="btn btn-sm btn-edit"
                  title="{{ __('m_config.policies.edit') }}"
                  data-toggle="modal"
                  data-target="#editSectionModal-{{ $s->section_id }}">
                  <i class="fas fa-edit"></i>
                </button>
                @endcan

                {{-- Toggle --}}
                @can('publish-policy-sections')
                <form action="{{ route('admin.policies.sections.toggle', [$policy, $s]) }}" method="POST"
                  class="d-inline js-confirm-toggle" data-active="{{ $s->is_active ? 1 : 0 }}">
                  @csrf
                  <button class="btn btn-sm {{ $s->is_active ? 'btn-toggle' : 'btn-secondary' }}"
                    title="{{ $s->is_active ? __('m_config.policies.deactivate_section') : __('m_config.policies.activate_section') }}"
                    data-toggle="tooltip">
                    <i class="fas {{ $s->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                  </button>
                </form>
                @endcan

                {{-- Eliminar --}}
                @can('delete-policy-sections')
                <form action="{{ route('admin.policies.sections.destroy', [$policy, $s]) }}" method="POST"
                  class="d-inline js-confirm-delete"
                  data-message="{{ __('m_config.policies.confirm_delete_section') }}">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-delete"
                    title="{{ __('m_config.policies.move_to_trash') }}" data-toggle="tooltip">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
                @endcan

                @else
                {{-- RESTORE --}}
                @can('delete-policy-sections')
                <form action="{{ route('admin.policies.sections.restore', [$policy, $s->section_id]) }}" method="POST"
                  class="d-inline js-confirm-restore"
                  data-message="{{ __('m_config.policies.confirm_restore_section') }}">
                  @csrf
                  <button class="btn btn-sm btn-restore"
                    title="{{ __('m_config.policies.restore') }}" data-toggle="tooltip">
                    <i class="fas fa-undo"></i>
                  </button>
                </form>
                @endcan

                {{-- FORCE DELETE --}}
                @can('delete-policy-sections')
                <form action="{{ route('admin.policies.sections.forceDestroy', [$policy, $s->section_id]) }}" method="POST"
                  class="d-inline js-confirm-force-delete"
                  data-message="{{ __('m_config.policies.confirm_force_delete_section') }}">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-force-delete"
                    title="{{ __('m_config.policies.delete_permanently') }}" data-toggle="tooltip">
                    <i class="fas fa-times-circle"></i>
                  </button>
                </form>
                @endcan
                @endif

              </div>
            </td>
          </tr>

          {{-- Modal Edit (edita traducción del LOCALE ACTUAL + base: orden/activo) --}}
          <div class="modal fade" id="editSectionModal-{{ $s->section_id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
              <div class="modal-content">
                <form action="{{ route('admin.policies.sections.update', [$policy, $s]) }}" method="POST" class="js-confirm-edit">
                  @csrf @method('PUT')
                  <div class="modal-header">
                    <h5 class="modal-title">{{ __('m_config.policies.edit_section') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                      <div class="row g-3 mb-3">
                        <div class="col-md-3">
                          <label class="form-label">{{ __('m_config.policies.order') }}</label>
                          <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $s->sort_order) }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                          <div class="form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1"
                              id="is_active_{{ $s->section_id }}" class="form-check-input" {{ $s->is_active ? 'checked' : '' }}>
                            <label for="is_active_{{ $s->section_id }}" class="form-check-label">{{ __('m_config.policies.active') }}</label>
                          </div>
                        </div>
                      </div>

                      <hr>

                      {{-- Tabs de idiomas --}}
                      <ul class="nav nav-tabs mb-3" id="policySectionTabs-{{ $s->section_id }}" role="tablist">
                        @foreach(['es', 'en', 'fr', 'pt', 'de'] as $lang)
                        <li class="nav-item" role="presentation">
                          <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                            id="tab-sec-{{ $s->section_id }}-{{ $lang }}"
                            data-toggle="tab"
                            data-target="#content-sec-{{ $s->section_id }}-{{ $lang }}"
                            type="button" role="tab">
                            {{ strtoupper($lang) }}
                            @php
                            // Check if translation exists for this locale using Spatie
                            $hasTrans = !empty($s->getTranslation('name', $lang, false));
                            @endphp
                            @if(!$hasTrans)
                            <span class="text-danger small ms-1" title="Sin traducción"><i class="fas fa-exclamation-circle"></i></span>
                            @endif
                          </button>
                        </li>
                        @endforeach
                      </ul>

                      <div class="tab-content" id="policySectionTabContent-{{ $s->section_id }}">
                        @foreach(['es', 'en', 'fr', 'pt', 'de'] as $lang)
                        @php
                        // Get translations using Spatie (false = no fallback)
                        $valName = $s->getTranslation('name', $lang, false) ?? '';
                        $valContent = $s->getTranslation('content', $lang, false) ?? '';
                        @endphp
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                          id="content-sec-{{ $s->section_id }}-{{ $lang }}"
                          role="tabpanel">

                          <div class="mb-3">
                            <label class="form-label">{{ __('m_config.policies.name') }} ({{ strtoupper($lang) }})</label>
                            <input type="text" name="translations[{{ $lang }}][name]" class="form-control"
                              value="{{ $valName }}">
                          </div>

                          <div class="mb-3">
                            <label class="form-label">{{ __('m_config.policies.translation_content') }} ({{ strtoupper($lang) }})</label>
                            <textarea name="translations[{{ $lang }}][content]" class="form-control" rows="10">{{ $valContent }}</textarea>
                          </div>
                        </div>
                        @endforeach
                      </div>
                  </div>
                  <div class="modal-footer">
                    <button class="btn btn-primary">{{ __('m_config.policies.save_changes') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('m_config.policies.close') }}</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          @empty
          <tr>
            <td colspan="5" class="text-center text-muted">{{ __('m_config.policies.no_sections') }}</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- Modal Create (name/content base → controller traduce a todos) --}}
<div class="modal fade" id="createSectionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <form action="{{ route('admin.policies.sections.store', $policy) }}" method="POST" class="js-confirm-create">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">{{ __('m_config.policies.new_section') }}</h5>
          <button type="button" class="close" data-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">{{ __('m_config.policies.order') }}</label>
              <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
              <div class="form-check">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                  id="is_active_new" class="form-check-input" {{ old('is_active', 1) ? 'checked' : '' }}>
                <label for="is_active_new" class="form-check-label">{{ __('m_config.policies.active') }}</label>
              </div>
            </div>

            <hr class="my-2">

            <div class="col-md-6">
              <label class="form-label">{{ __('m_config.policies.name') }}</label>
              <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
              <small class="text-muted">{{ __('m_config.policies.lang_autodetect_hint') ?? 'Puedes escribir en cualquier idioma; se detecta automáticamente.' }}</small>
            </div>
            <div class="col-12">
              <label class="form-label">{{ __('m_config.policies.translation_content') }}</label>
              <textarea name="content" class="form-control" rows="10" required>{{ old('content') }}</textarea>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary">{{ __('m_config.policies.save') }}</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('m_config.policies.close') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>
@stop

@push('css')
<style>
  .btn-edit {
    background: #0dcaf0;
    color: #000;
  }

  .btn-edit:hover {
    filter: brightness(.95);
  }

  .btn-toggle {
    background: #198754;
    color: #fff;
  }

  .btn-delete {
    background: #dc3545;
    color: #fff;
  }

  .btn-delete:hover {
    filter: brightness(.95);
  }

  .btn-restore {
    background: #17a2b8;
    color: #fff;
  }

  .btn-restore:hover {
    filter: brightness(.95);
    color: #fff;
  }

  .btn-force-delete {
    background: #dc3545;
    color: #fff;
    border: 1px solid #dc3545;
  }

  .btn-force-delete:hover {
    background: #bb2d3b;
    color: #fff;
  }
</style>
@endpush

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
  Swal.fire({
    icon: 'success',
    title: @json(__(session('success'))),
    showConfirmButton: false,
    timer: 1800
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
  [...document.querySelectorAll('[data-toggle="tooltip"]')].forEach(el => new bootstrap.Tooltip(el));

  // limpiar backdrops duplicados
  document.addEventListener('hidden.bs.modal', () => {
    const backs = document.querySelectorAll('.modal-backdrop');
    if (backs.length > 1) backs.forEach((b, i) => {
      if (i < backs.length - 1) b.remove();
    });
  });

  const confirm = (opts) => Swal.fire(Object.assign({
    icon: 'question',
    showCancelButton: true,
    cancelButtonColor: '#6c757d'
  }, opts));

  // Crear
  document.querySelectorAll('.js-confirm-create').forEach(form => {
    form.addEventListener('submit', ev => {
      ev.preventDefault();
      confirm({
        title: @json(__('m_config.policies.confirm_create_section') ?? '¿Crear esta sección?'),
        confirmButtonColor: '#28a745',
        confirmButtonText: @json(__('m_config.policies.create') ?? 'Crear'),
        cancelButtonText: @json(__('m_config.policies.cancel') ?? 'Cancelar'),
      }).then(r => {
        if (r.isConfirmed) form.submit();
      });
    });
  });

  // Editar
  document.querySelectorAll('.js-confirm-edit').forEach(form => {
    form.addEventListener('submit', ev => {
      ev.preventDefault();
      confirm({
        title: @json(__('m_config.policies.confirm_edit_section') ?? '¿Guardar cambios?'),
        confirmButtonColor: '#0d6efd',
        confirmButtonText: @json(__('m_config.policies.save_changes') ?? 'Guardar cambios'),
        cancelButtonText: @json(__('m_config.policies.cancel') ?? 'Cancelar'),
      }).then(r => {
        if (r.isConfirmed) form.submit();
      });
    });
  });

  // Eliminar
  document.querySelectorAll('.js-confirm-delete').forEach(form => {
    form.addEventListener('submit', ev => {
      ev.preventDefault();
      confirm({
        title: form.dataset.message || @json(__('m_config.policies.confirm_delete_section')),
        icon: 'warning',
        confirmButtonColor: '#d33',
        confirmButtonText: @json(__('m_config.policies.delete') ?? 'Eliminar'),
        cancelButtonText: @json(__('m_config.policies.cancel') ?? 'Cancelar'),
      }).then(r => {
        if (r.isConfirmed) form.submit();
      });
    });
  });

  // Toggle
  document.querySelectorAll('.js-confirm-toggle').forEach(form => {
    form.addEventListener('submit', ev => {
      ev.preventDefault();
      const isActive = form.dataset.active === '1';
      confirm({
        title: isActive ? @json(__('m_config.policies.confirm_deactivate_section')) : @json(__('m_config.policies.confirm_activate_section')),
        confirmButtonColor: isActive ? '#d33' : '#28a745',
        confirmButtonText: isActive ? @json(__('m_config.policies.deactivate') ?? 'Desactivar') : @json(__('m_config.policies.activate') ?? 'Activar'),
        cancelButtonText: @json(__('m_config.policies.cancel') ?? 'Cancelar'),
      }).then(r => {
        if (r.isConfirmed) form.submit();
      });
    });
  });
  });
  });

  // Restore
  document.querySelectorAll('.js-confirm-restore').forEach(form => {
    form.addEventListener('submit', ev => {
      ev.preventDefault();
      confirm({
        title: form.dataset.message,
        icon: 'question',
        confirmButtonColor: '#17a2b8',
        confirmButtonText: @json(__('m_config.policies.restore') ?? 'Restaurar'),
        cancelButtonText: @json(__('m_config.policies.cancel') ?? 'Cancelar'),
      }).then(r => {
        if (r.isConfirmed) form.submit();
      });
    });
  });

  // Force delete
  document.querySelectorAll('.js-confirm-force-delete').forEach(form => {
  form.addEventListener('submit', ev => {
    ev.preventDefault();
    confirm({
      title: form.dataset.message,
      icon: 'warning',
      confirmButtonColor: '#dc3545',
      confirmButtonText: @json(__('m_config.policies.delete_permanently') ?? 'Eliminar permanentemente'),
      cancelButtonText: @json(__('m_config.policies.cancel') ?? 'Cancelar'),
    }).then(r => {
      if (r.isConfirmed) form.submit();
    });
  });
  });

  });
</script>
@stop