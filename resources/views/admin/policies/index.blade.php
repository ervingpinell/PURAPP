{{-- resources/views/admin/policies/index.blade.php --}}
@extends('adminlte::page')

@section('title', __('m_config.policies.categories_title'))

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
  <h1 class="m-0">
    <i class="fas fa-shield-alt"></i> {{ __('m_config.policies.categories_title') }}
    <small class="text-muted">({{ strtoupper(app()->getLocale()) }})</small>
  </h1>

  @can('create-policies')
  <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createPolicyModal">
    <i class="fas fa-plus"></i> {{ __('m_config.policies.new_category') }}
  </button>
  @endcan
</div>
@stop

@section('content')
{{-- Fallback sin JS --}}
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

{{-- Barra de filtros (igual estilo que tours) --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
  <div class="btn-group" role="group" aria-label="{{ __('m_config.policies.filter_status_aria') }}">
    <a href="{{ route('admin.policies.index', ['status' => 'active']) }}"
      class="btn btn-outline-primary {{ ($status ?? 'active') === 'active' ? 'active' : '' }}">
      {{ __('m_config.policies.filter_active') }}
    </a>
    <a href="{{ route('admin.policies.index', ['status' => 'inactive']) }}"
      class="btn btn-outline-primary {{ ($status ?? '') === 'inactive' ? 'active' : '' }}">
      {{ __('m_config.policies.filter_inactive') }}
    </a>
    <a href="{{ route('admin.policies.index', ['status' => 'archived']) }}"
      class="btn btn-outline-primary {{ ($status ?? '') === 'archived' ? 'active' : '' }}">
      {{ __('m_config.policies.filter_archived') }}
    </a>
    <a href="{{ route('admin.policies.index', ['status' => 'all']) }}"
      class="btn btn-outline-secondary {{ ($status ?? '') === 'all' ? 'active' : '' }}">
      {{ __('m_config.policies.filter_all') }}
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
            <th class="text-center">{{ __('m_config.policies.title_current_locale') }}</th>
            <th class="text-center">{{ __('m_config.policies.slug') }}</th>
            @if(($status ?? 'active') === 'archived')
            <th>{{ __('m_config.policies.deleted_by') }}</th>
            <th>{{ __('m_config.policies.deleted_at') }}</th>
            @else
            <th>{{ __('m_config.policies.validity_range') }}</th>
            <th>{{ __('m_config.policies.status') }}</th>
            @endif
            <th>{{ __('m_config.policies.sections') }}</th>
            <th>{{ __('m_config.policies.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($policies as $p)
          @php
          $t = $p->translation();
          $from = $p->effective_from ? \Illuminate\Support\Carbon::parse($p->effective_from)->format('d-M-Y') : null;
          $to = $p->effective_to ? \Illuminate\Support\Carbon::parse($p->effective_to)->format('d-M-Y') : null;
          $isTrashed = method_exists($p, 'trashed') && $p->trashed();
          @endphp
          {{-- Quitamos el fondo amarillo: todas las filas con mismo color --}}
          <tr class="text-center">
            <td>{{ $p->policy_id }}</td>
            <td class="text-center">
              @if($isTrashed)
              <i class="fas fa-trash-alt text-muted me-1"></i>
              @endif
              {{ $t?->name ?? '—' }}
            </td>
            <td class="text-center"><code class="text-muted">{{ $p->slug }}</code></td>
            @if(($status ?? 'active') === 'archived')
            <td>
              @if($p->deletedBy)

              <span class="badge bg-secondary">
                <i class="fas fa-user-times"></i> {{ $p->deletedBy->first_name }} {{ $p->deletedBy->last_name }}
              </span>
              @else
              <span class="text-muted">—</span>
              @endif
            </td>
            <td>
              @if($p->deleted_at)
              {{ \Illuminate\Support\Carbon::parse($p->deleted_at)->format('d-M-Y H:i') }}
              @else
              <span class="text-muted">—</span>
              @endif
            </td>
            @else
            <td>
              @if($from || $to)
              {{ $from ?? '—' }} &rarr; {{ $to ?? '—' }}
              @else
              <span class="text-muted">—</span>
              @endif
            </td>
            <td>
              @if($isTrashed)
              <span class="badge bg-warning text-dark">
                <i class="fas fa-trash-alt"></i>
                {{ __('m_config.policies.in_trash') }}
              </span>
              @else
              <span class="badge {{ $p->is_active ? 'bg-success' : 'bg-secondary' }}">
                <i class="fas {{ $p->is_active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                {{ $p->is_active ? __('m_config.policies.active') : __('m_config.policies.inactive') }}
              </span>
              @endif
            </td>
            @endif
            <td>{{ $p->sections_count ?? $p->sections()->count() }}</td>
            <td>
              <div class="actions text-center my-1">
                @if(!$isTrashed)
                {{-- Ver secciones --}}
                <a class="btn btn-info btn-sm me-1"
                  href="{{ route('admin.policies.sections.index', $p) }}"
                  title="{{ __('m_config.policies.view_sections') }}" data-bs-toggle="tooltip">
                  <i class="fas fa-eye"></i>
                </a>

                {{-- Editar --}}
                @can('edit-policies')
                <button class="btn btn-edit btn-sm me-1"
                  data-bs-toggle="modal"
                  data-bs-target="#editPolicyModal-{{ $p->policy_id }}"
                  title="{{ __('m_config.policies.edit') }}">
                  <i class="fas fa-edit"></i>
                </button>
                @endcan

                {{-- Toggle activo/inactivo --}}
                @can('edit-policies')
                <form class="d-inline me-1 js-confirm-toggle" method="POST"
                  action="{{ route('admin.policies.toggle', $p) }}"
                  data-active="{{ $p->is_active ? 1 : 0 }}">
                  @csrf
                  <button class="btn {{ $p->is_active ? 'btn-toggle' : 'btn-secondary' }} btn-sm"
                    title="{{ $p->is_active ? __('m_config.policies.deactivate_category') : __('m_config.policies.activate_category') }}"
                    data-bs-toggle="tooltip">
                    <i class="fas {{ $p->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                  </button>
                </form>
                @endcan

                {{-- Eliminar -> papelera (soft delete) --}}
                @can('delete-policies')
                <form class="d-inline js-confirm-delete" method="POST"
                  action="{{ route('admin.policies.destroy', $p) }}"
                  data-message="{{ __('m_config.policies.delete_category_confirm') }}">
                  @csrf @method('DELETE')
                  <button class="btn btn-delete btn-sm"
                    title="{{ __('m_config.policies.move_to_trash') }}" data-bs-toggle="tooltip">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
                @endcan
                @else
                {{-- Restaurar desde papelera --}}
                @can('delete-policies')
                <form class="d-inline js-confirm-restore" method="POST"
                  action="{{ route('admin.policies.restore', $p->policy_id) }}"
                  data-message="{{ __('m_config.policies.restore_category_confirm') }}">
                  @csrf
                  <button class="btn btn-restore btn-sm me-1"
                    title="{{ __('m_config.policies.restore') }}" data-bs-toggle="tooltip">
                    <i class="fas fa-undo"></i>
                  </button>
                </form>
                @endcan

                {{-- Eliminar definitivamente (solo admins) --}}
                @can('delete-policies')
                <form class="d-inline js-confirm-force-delete" method="POST"
                  action="{{ route('admin.policies.forceDestroy', $p->policy_id) }}"
                  data-message="{{ __('m_config.policies.force_delete_confirm') }}">
                  @csrf @method('DELETE')
                  <button class="btn btn-force-delete btn-sm"
                    title="{{ __('m_config.policies.delete_permanently') }}" data-bs-toggle="tooltip">
                    <i class="fas fa-times-circle"></i>
                  </button>
                </form>
                @endcan
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center text-muted p-4">
              {{ __('m_config.policies.no_categories') }}
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- MODAL: Nueva categoría (base) --}}
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
              <input type="checkbox" name="is_active" value="1" class="form-check-input" id="p-active-new" checked>
              <label class="form-check-label" for="p-active-new">{{ __('m_config.policies.active') }}</label>
            </div>
          </div>
        </div>

        <hr>

        <div class="mb-3">
          <label class="form-label">{{ __('m_config.policies.name') }}</label>
          <input type="text" name="name" class="form-control" required>
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
          <label class="form-label">
            {{ __('m_config.policies.type') }}
            <span class="badge bg-secondary ms-1">{{ __('m_config.policies.type_optional') }}</span>
          </label>
          <select name="type" class="form-select">
            <option value="">{{ __('m_config.policies.type_none') }}</option>
            @foreach(\App\Models\Policy::TYPES as $typeKey => $typeLabel)
            <option value="{{ $typeKey }}">{{ $typeLabel }}</option>
            @endforeach
          </select>
          <small class="text-muted d-block mt-1">
            <i class="fas fa-info-circle"></i> {{ __('m_config.policies.type_description') }}
          </small>
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

{{-- MODALES: Editar categoría (traducción del locale actual + propagación opcional) --}}
@foreach ($policies as $p)
@php
$fromVal = $p->effective_from ? \Illuminate\Support\Carbon::parse($p->effective_from)->format('d-M-Y') : '';
$toVal = $p->effective_to ? \Illuminate\Support\Carbon::parse($p->effective_to)->format('d-M-Y') : '';
$t = $p->translation();
@endphp
<div class="modal fade" id="editPolicyModal-{{ $p->policy_id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <form class="modal-content" method="POST" action="{{ route('admin.policies.update', $p) }}">
      @csrf @method('PUT')

      {{-- idioma que estás editando (traducción) --}}
      <input type="hidden" name="locale" value="{{ app()->getLocale() }}">

      <div class="modal-header">
        <h5 class="modal-title">
          {{ __('m_config.policies.edit_category') }}
          <small class="text-muted ms-2">({{ strtoupper(app()->getLocale()) }})</small>
        </h5>
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

        {{-- Tabs de idiomas --}}
        <ul class="nav nav-tabs mb-3" id="policyTabs-{{ $p->policy_id }}" role="tablist">
          @foreach(['es', 'en', 'fr', 'pt', 'de'] as $lang)
          <li class="nav-item" role="presentation">
            <button class="nav-link {{ $loop->first ? 'active' : '' }}"
              id="tab-{{ $p->policy_id }}-{{ $lang }}"
              data-bs-toggle="tab"
              data-bs-target="#content-{{ $p->policy_id }}-{{ $lang }}"
              type="button" role="tab">
              {{ strtoupper($lang) }}
              @php
              // Indicador visual si falta traducción
              $hasTrans = $p->translations->where('locale', $lang)->first();
              @endphp
              @if(!$hasTrans)
              <span class="text-danger small ms-1" title="Sin traducción"><i class="fas fa-exclamation-circle"></i></span>
              @endif
            </button>
          </li>
          @endforeach
        </ul>

        <div class="tab-content" id="policyTabContent-{{ $p->policy_id }}">
          @foreach(['es', 'en', 'fr', 'pt', 'de'] as $lang)
          @php
          $trans = $p->translations->firstWhere('locale', $lang);
          $valName = $trans ? $trans->name : '';
          $valContent = $trans ? $trans->content : '';
          @endphp
          <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
            id="content-{{ $p->policy_id }}-{{ $lang }}"
            role="tabpanel">

            <div class="mb-3">
              <label class="form-label">{{ __('m_config.policies.name') }} ({{ strtoupper($lang) }})</label>
              <input type="text" name="translations[{{ $lang }}][name]" class="form-control"
                value="{{ $valName }}">
            </div>

            <div class="mb-3">
              <label class="form-label">{{ __('m_config.policies.description_label') }} ({{ strtoupper($lang) }})</label>
              <textarea name="translations[{{ $lang }}][content]" class="form-control" rows="8">{{ $valContent }}</textarea>
            </div>
          </div>
          @endforeach
        </div>

        {{-- SLUG (base) --}}
        <div class="mb-3 mt-4">
          <label class="form-label">
            {{ __('m_config.policies.slug') }}
            <span class="text-muted small">({{ __('m_config.policies.slug_hint') }})</span>
          </label>
          <input type="text" name="slug" class="form-control" value="{{ old('slug', $p->slug) }}">
          <small class="text-muted">
            <i class="fas fa-info-circle"></i> {{ __('m_config.policies.slug_edit_hint') }}
          </small>
        </div>

        {{-- TYPE (base) --}}
        <div class="mb-3">
          <label class="form-label">
            {{ __('m_config.policies.type') }}
            <span class="badge bg-secondary ms-1">{{ __('m_config.policies.type_optional') }}</span>
          </label>
          <select name="type" class="form-select">
            <option value="">{{ __('m_config.policies.type_none') }}</option>
            @foreach(\App\Models\Policy::TYPES as $typeKey => $typeLabel)
            <option value="{{ $typeKey }}" {{ old('type', $p->type) === $typeKey ? 'selected' : '' }}>
              {{ $typeLabel }}
            </option>
            @endforeach
          </select>
          <small class="text-muted d-block mt-1">
            <i class="fas fa-info-circle"></i> {{ __('m_config.policies.type_description') }}
          </small>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-primary">
          <i class="fas fa-save"></i> {{ __('m_config.policies.save_changes') }}
        </button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          {{ __('m_config.policies.close') }}
        </button>
      </div>
    </form>
  </div>
</div>
@endforeach
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

  /* papelera en rojo como otros destructivos */
  .btn-delete:hover {
    filter: brightness(.95);
  }

  .btn-restore {
    background: #198754;
    color: #fff;
  }

  .btn-restore:hover {
    filter: brightness(.95);
  }

  .btn-force-delete {
    background: #dc3545;
    color: #fff;
  }

  .btn-force-delete:hover {
    filter: brightness(.95);
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
    // Tooltips
    [...document.querySelectorAll('[data-bs-toggle="tooltip"]')]
    .forEach(el => new bootstrap.Tooltip(el));

    // Confirmar eliminar (mover a papelera)
    document.querySelectorAll('.js-confirm-delete').forEach(form => {
      form.addEventListener('submit', ev => {
        ev.preventDefault();
        const msg = form.dataset.message || @json(__('m_config.policies.delete_category_confirm'));
        Swal.fire({
          title: msg,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#dc3545',
          cancelButtonColor: '#6c757d',
          confirmButtonText: @json(__('m_config.policies.move_to_trash')),
          cancelButtonText: @json(__('m_config.policies.cancel')),
        }).then(res => {
          if (res.isConfirmed) form.submit();
        });
      });
    });

    // Confirmar toggle
    document.querySelectorAll('.js-confirm-toggle').forEach(form => {
      form.addEventListener('submit', ev => {
        ev.preventDefault();
        const isActive = form.dataset.active === '1';
        const titleVar = isActive ?
          @json(__('m_config.policies.deactivate_category')) :
          @json(__('m_config.policies.activate_category'));

        Swal.fire({
          title: titleVar + '?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: isActive ? '#d33' : '#28a745',
          cancelButtonColor: '#6c757d',
          confirmButtonText: titleVar,
          cancelButtonText: @json(__('m_config.policies.cancel')),
        }).then(res => {
          if (res.isConfirmed) form.submit();
        });
      });
    });

    // Confirmar restaurar
    document.querySelectorAll('.js-confirm-restore').forEach(form => {
      form.addEventListener('submit', ev => {
        ev.preventDefault();
        const msg = form.dataset.message || @json(__('m_config.policies.restore_category_confirm'));
        Swal.fire({
          title: msg,
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#198754',
          cancelButtonColor: '#6c757d',
          confirmButtonText: @json(__('m_config.policies.restore')),
          cancelButtonText: @json(__('m_config.policies.cancel')),
        }).then(res => {
          if (res.isConfirmed) form.submit();
        });
      });
    });

    // Confirmar borrado definitivo
    document.querySelectorAll('.js-confirm-force-delete').forEach(form => {
      form.addEventListener('submit', ev => {
        ev.preventDefault();
        const msg = form.dataset.message || @json(__('m_config.policies.force_delete_confirm'));
        Swal.fire({
          title: msg,
          icon: 'error',
          showCancelButton: true,
          confirmButtonColor: '#dc3545',
          cancelButtonColor: '#6c757d',
          confirmButtonText: @json(__('m_config.policies.delete_permanently')),
          cancelButtonText: @json(__('m_config.policies.cancel')),
        }).then(res => {
          if (res.isConfirmed) form.submit();
        });
      });
    });
  });
</script>
@stop