@extends('adminlte::page')

@section('title', __('hotels.title'))

@section('content_header')
    <h1>{{ __('hotels.header') }}</h1>
@stop

@section('content')
    {{-- Botón para ordenar --}}
    <form action="{{ route('admin.hotels.sort') }}" method="POST" class="mb-3">
        @csrf
        <button type="submit" class="btn btn-view">
            <i class="fas fa-sort-alpha-down"></i> {{ __('hotels.sort_alpha') }}
        </button>
    </form>

    {{-- Crear hotel --}}
    <form action="{{ route('admin.hotels.store') }}" method="POST" class="mb-4" autocomplete="off">
        @csrf
        <div class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label">{{ __('hotels.name') }}</label>
                <input
                    type="text"
                    name="name"
                    class="form-control"
                    placeholder="{{ __('hotels.name_placeholder') }}"
                    value="{{ old('name') }}"
                    required
                >
            </div>
            <div class="col-md-2">
                <button class="btn btn-edit w-100" type="submit">
                    <i class="fas fa-plus"></i> {{ __('hotels.add') }}
                </button>
            </div>
        </div>
    </form>

    {{-- Tabla --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="bg-primary text-white">
                <tr class="text-center">
                    <th>{{ __('hotels.name') }}</th>
                    <th>{{ __('hotels.status') }}</th>
                    <th style="width:260px">{{ __('hotels.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($hotels as $hotel)
                    <tr>
                        <td class="fw-semibold">{{ $hotel->name }}</td>
                        <td class="text-center">
                            <span class="badge {{ $hotel->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $hotel->is_active ? __('hotels.active') : __('hotels.inactive') }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex flex-wrap gap-2">
                                {{-- ✏️ Editar (modal único) --}}
                                <button
                                    class="btn btn-edit btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editHotelModal"
                                    data-id="{{ $hotel->hotel_id }}"
                                    data-name="{{ $hotel->name }}"
                                    data-active="{{ $hotel->is_active ? 1 : 0 }}"
                                    title="{{ __('hotels.edit') }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                {{-- 🔁 Activar/Desactivar --}}
                                <form action="{{ route('admin.hotels.toggle', $hotel->hotel_id) }}"
                                      method="POST"
                                      class="d-inline toggle-form"
                                      data-name="{{ $hotel->name }}"
                                      data-active="{{ $hotel->is_active ? 1 : 0 }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="btn btn-toggle btn-sm"
                                        title="{{ $hotel->is_active ? __('hotels.deactivate') : __('hotels.activate') }}">
                                        <i class="fas fa-toggle-{{ $hotel->is_active ? 'on' : 'off' }}"></i>
                                    </button>
                                </form>

                                {{-- 🗑️ Eliminar definitivo --}}
                                <form action="{{ route('admin.hotels.destroy', $hotel->hotel_id) }}"
                                      method="POST"
                                      class="d-inline delete-form"
                                      data-name="{{ $hotel->name }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-delete btn-sm" title="{{ __('hotels.delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">{{ __('hotels.no_records') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL ÚNICO: Editar hotel --}}
    <div class="modal fade" id="editHotelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editHotelForm" action="#" method="POST" class="modal-content" autocomplete="off">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('hotels.edit_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('hotels.close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('hotels.name') }}</label>
                        <input
                            type="text"
                            name="name"
                            id="hotelNameInput"
                            class="form-control"
                            placeholder="{{ __('hotels.name_placeholder') }}"
                            required
                        >
                    </div>
                    <input type="hidden" name="is_active" value="0">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="hotelActiveInput" name="is_active" value="1">
                        <label class="form-check-label" for="hotelActiveInput">{{ __('hotels.active') }}</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-edit">
                        <i class="fas fa-save"></i> {{ __('hotels.save_changes') }}
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('hotels.cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ---- i18n strings para JS (inyectadas desde PHP) ----
const I18N = {
  // Confirmaciones (toggle activación)
  confirmActivateTitle: @json(__('hotels.confirm_activate_title')),
  confirmActivateText:  @json(__('hotels.confirm_activate_text')),
  confirmDeactivateTitle: @json(__('hotels.confirm_deactivate_title')),
  confirmDeactivateText:  @json(__('hotels.confirm_deactivate_text')),
  // Confirmación eliminar
  confirmDeleteTitle: @json(__('hotels.confirm_delete_title')),
  confirmDeleteText:  @json(__('hotels.confirm_delete_text')),
  // Botones genéricos
  confirmBtn: @json(__('hotels.save_changes')), // usamos un texto genérico positivo
  cancel: @json(__('hotels.cancel')),
  errorTitle: @json(__('hotels.error_title')),
};

// ---- Rellena el modal de edición ----
document.getElementById('editHotelModal')?.addEventListener('show.bs.modal', function (ev) {
  const btn = ev.relatedTarget;
  if (!btn) return;
  const id     = btn.getAttribute('data-id');
  const name   = btn.getAttribute('data-name') || '';
  const active = btn.getAttribute('data-active') === '1';
  const form   = document.getElementById('editHotelForm');
  form.action  = "{{ route('admin.hotels.update', '__ID__') }}".replace('__ID__', id);
  document.getElementById('hotelNameInput').value = name;
  document.getElementById('hotelActiveInput').checked = active;
});

// Utilidad para reemplazar :name en las cadenas
const withName = (tpl, name) => (tpl || '').replace(':name', name || '');

// ---- Confirmación Activar/Desactivar ----
document.querySelectorAll('.toggle-form').forEach(form => {
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const name = form.getAttribute('data-name') || '';
    const isActive = form.getAttribute('data-active') === '1';

    const title = isActive ? I18N.confirmDeactivateTitle : I18N.confirmActivateTitle;
    const text  = isActive ? I18N.confirmDeactivateText : I18N.confirmActivateText;

    Swal.fire({
      title: withName(title, name),
      text:  withName(text,  name),
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ffc107',
      cancelButtonColor: '#6c757d',
      confirmButtonText: I18N.confirmBtn,
      cancelButtonText: I18N.cancel
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit();
      }
    });
  });
});

// ---- Confirmación Eliminar ----
document.querySelectorAll('.delete-form').forEach(form => {
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const name = form.getAttribute('data-name') || '';

    Swal.fire({
      title: withName(I18N.confirmDeleteTitle, name),
      text:  withName(I18N.confirmDeleteText,  name),
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: I18N.confirmBtn,
      cancelButtonText: I18N.cancel
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit();
      }
    });
  });
});

// ---- SweetAlert para mensajes de éxito o error ----
@if (session('success'))
  Swal.fire({
    icon: 'success',
    title: @json(__('hotels.title')),
    text: @json(session('success')),
    timer: 2500,
    showConfirmButton: false
  });
@endif

@if (session('error'))
  Swal.fire({
    icon: 'error',
    title: @json(__('hotels.error_title')),
    text: @json(session('error')),
    timer: 2500,
    showConfirmButton: false
  });
@endif
</script>
@stop
