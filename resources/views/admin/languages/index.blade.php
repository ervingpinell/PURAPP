@extends('adminlte::page')

@section('title', __('m_tours.language.ui.page_title'))

@section('content_header')
<h1>{{ __('m_tours.language.ui.page_heading') }}</h1>
@stop

@section('content')
<div class="card card-primary card-outline card-tabs">
  <div class="card-header p-0 pt-1 border-bottom-0">
    <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" data-toggle="pill" href="#tab-active" role="tab">
          <i class="fas fa-list me-1"></i> {{ __('m_tours.language.ui.list_title') }}
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.languages.trash') }}" role="tab">
          <i class="fas fa-trash me-1"></i> {{ __('m_tours.language.ui.trash_title') }}
        </a>
      </li>
    </ul>
  </div>
  <div class="card-body">
    <div class="p-3 table-responsive">
      @can('create-tour-languages')
      <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
        <i class="fas fa-plus"></i> {{ __('m_tours.language.ui.add') }}
      </a>
      @endcan

      <table class="table table-bordered table-striped table-hover align-middle">
        <thead class="bg-primary text-white">
          <tr>
            <th>{{ __('m_tours.language.ui.table.id') }}</th>
            <th>{{ __('m_tours.language.ui.table.name') }}</th>
            <th class="text-center">{{ __('m_tours.language.ui.table.state') }}</th>
            <th class="text-center" style="width:220px;">{{ __('m_tours.language.ui.table.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($languages as $language)
          <tr>
            <td>{{ $language->tour_language_id }}</td>
            <td>{{ $language->name }}</td>
            <td class="text-center">
              @if ($language->is_active)
              <span class="badge bg-success">{{ __('m_tours.language.status.active') }}</span>
              @else
              <span class="badge bg-secondary">{{ __('m_tours.language.status.inactive') }}</span>
              @endif
            </td>
            <td class="text-center">
              {{-- Editar --}}
              @can('edit-tour-languages')
              <a href="#" class="btn btn-edit btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#modalEditar{{ $language->tour_language_id }}"
                title="{{ __('m_tours.language.ui.edit_title') }}">
                <i class="fas fa-edit"></i>
              </a>
              @endcan

              {{-- Alternar activar/desactivar (PATCH) --}}
              @can('publish-tour-languages')
              <form action="{{ route('admin.languages.toggle', $language->tour_language_id) }}"
                method="POST"
                class="d-inline form-toggle-language"
                data-name="{{ $language->name }}"
                data-active="{{ $language->is_active ? 1 : 0 }}">
                @csrf
                @method('PATCH')
                <button type="submit"
                  class="btn btn-sm btn-{{ $language->is_active ? 'warning' : 'secondary' }}"
                  title="{{ $language->is_active ? 'Desactivar' : 'Activar' }}">
                  <i class="fas fa-toggle-{{ $language->is_active ? 'on' : 'off' }}"></i>
                </button>
              </form>
              @endcan

              {{-- Delete Button (Soft Delete) --}}
              @can('soft-delete-tour-languages')
              <form action="{{ route('admin.languages.destroy', $language->tour_language_id) }}"
                method="POST"
                class="d-inline form-delete-language"
                data-name="{{ $language->name }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger"
                  title="{{ __('m_tours.language.ui.delete') }}">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
              @endcan
            </td>
          </tr>

          {{-- Modal Editar --}}
          <div class="modal fade" id="modalEditar{{ $language->tour_language_id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
              <form action="{{ route('admin.languages.update', $language->tour_language_id) }}"
                method="POST"
                class="form-edit-language">
                @csrf
                @method('PUT')
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">{{ __('m_tours.language.ui.edit_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('m_tours.language.ui.close') }}"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label">{{ __('m_tours.language.fields.name') }}</label>
                      <input type="text" name="name" class="form-control" value="{{ $language->name }}" required>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">
                      <i class="fas fa-save me-1"></i> {{ __('m_tours.language.ui.update') }}
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_tours.language.ui.cancel') }}</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- Modal Registrar --}}
<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('admin.languages.store') }}" method="POST" class="form-create-language">
      @csrf
      <input type="hidden" name="_from" value="create">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ __('m_tours.language.ui.create_title') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('m_tours.language.ui.close') }}"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">{{ __('m_tours.language.fields.name') }}</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> {{ __('m_tours.language.ui.save') }}
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_tours.language.ui.cancel') }}</button>
        </div>
      </div>
    </form>
  </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // ===== Utilidad: spinner + lock (solo botones; NO deshabilita inputs) =====
  function lockAndSubmit(form, loadingText = @json(__('m_tours.language.ui.processing'))) {
    if (!form.checkValidity()) {
      if (typeof form.reportValidity === 'function') form.reportValidity();
      return;
    }
    const buttons = form.querySelectorAll('button');
    let submitBtn = form.querySelector('button[type="submit"]') || buttons[0];

    buttons.forEach(btn => {
      if (submitBtn && btn === submitBtn) return;
      btn.disabled = true;
    });

    if (submitBtn) {
      if (!submitBtn.dataset.originalHtml) submitBtn.dataset.originalHtml = submitBtn.innerHTML;
      submitBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' +
        loadingText;
      submitBtn.classList.add('disabled');
      submitBtn.disabled = true;
    }

    form.querySelectorAll('input,select,textarea').forEach(el => {
      if (el.disabled) el.disabled = false;
    });

    form.submit();
  }

  // ===== Alternar activar/desactivar =====
  document.querySelectorAll('.form-toggle-language').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const name = form.getAttribute('data-name') || @json(__('m_tours.language.ui.item_this'));
      const isActive = form.getAttribute('data-active') === '1';

      Swal.fire({
        title: isActive ? @json(__('m_tours.language.ui.toggle_confirm_off_title')) : @json(__('m_tours.language.ui.toggle_confirm_on_title')),
        html: (isActive ?
          @json(__('m_tours.language.ui.toggle_confirm_off_html', ['label' => ':label'])) :
          @json(__('m_tours.language.ui.toggle_confirm_on_html', ['label' => ':label']))
        ).replace(':label', name),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: @json(__('m_tours.language.ui.yes_continue')),
        cancelButtonText: @json(__('m_tours.language.ui.cancel')),
        confirmButtonColor: isActive ? '#ffc107' : '#28a745',
        cancelButtonColor: '#6c757d'
      }).then(res => {
        if (res.isConfirmed) {
          lockAndSubmit(form, isActive ? @json(__('m_tours.language.ui.deactivating')) :
            @json(__('m_tours.language.ui.activating')));
        }
      });
    });
  });

  // ===== Editar (confirmación + spinner) =====
  document.querySelectorAll('.form-edit-language').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      Swal.fire({
        title: @json(__('m_tours.language.ui.edit_confirm_title')),
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: @json(__('m_tours.language.ui.edit_confirm_button')),
        cancelButtonText: @json(__('m_tours.language.ui.cancel')),
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d'
      }).then(res => {
        if (res.isConfirmed) {
          lockAndSubmit(form, @json(__('m_tours.language.ui.saving')));
        }
      });
    });
  });

  // ===== Eliminar (Soft Delete) =====
  document.querySelectorAll('.form-delete-language').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const name = form.getAttribute('data-name') || @json(__('m_tours.language.ui.item_this'));
      Swal.fire({
        title: @json(__('m_tours.language.alerts.delete_title')),
        text: @json(__('m_tours.language.alerts.delete_text')),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: @json(__('m_tours.language.ui.yes_delete')),
        cancelButtonText: @json(__('m_tours.language.ui.cancel')),
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d'
      }).then(res => {
        if (res.isConfirmed) {
          // We use lockAndSubmit but with text 'Deleting...'
          lockAndSubmit(form, @json(__('m_tours.language.ui.deleting')));
        }
      });
    });
  });

  // ===== Flash messages =====
  @if(session('success'))
  Swal.fire({
    icon: 'success',
    title: @json(__('m_tours.common.success_title')),
    text: @json(session('success')),
    timer: 2200,
    showConfirmButton: false
  });
  @endif

  @if($errors -> any())
  Swal.fire({
    icon: 'error',
    title: @json(__('m_tours.language.error.save')),
    html: `<ul style="text-align:left;margin:0;padding-left:18px;">{!! collect($errors->all())->map(fn($e)=>"<li>".e($e)."</li>")->implode('') !!}</ul>`,
    confirmButtonColor: '#d33'
  }).then(() => {
    // Si el error fue al crear, abrimos el modal Registrar otra vez
    @if(old('_from') === 'create')
    const el = document.getElementById('modalRegistrar');
    if (el && typeof bootstrap !== 'undefined') new bootstrap.Modal(el).show();
    @endif
  });
  @endif
</script>
@stop
