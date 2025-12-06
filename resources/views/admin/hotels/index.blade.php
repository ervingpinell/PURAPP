{{-- resources/views/admin/hotels/index.blade.php --}}
@extends('adminlte::page')

@section('title', __('pickups.hotels.title'))

@section('content_header')
<h1 class="d-flex align-items-center gap-2">
    <i class="fas fa-hotel text-primary"></i>
    {{ __('pickups.hotels.header') }}
</h1>
@stop

@push('css')
<style>
    /* ===== Botones semánticos (combina con AdminLTE) ===== */
    .btn-view {
        background: #0dcaf0;
        border-color: #0dcaf0;
        color: #0b2a2e;
    }

    .btn-edit {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }

    .btn-toggle {
        background: #ffc107;
        border-color: #ffc107;
        color: #2b2b2b;
    }

    .btn-delete {
        background: #dc3545;
        border-color: #dc3545;
        color: #fff;
    }

    .btn-view:hover {
        filter: brightness(0.95);
        color: #072125;
    }

    .btn-edit:hover,
    .btn-toggle:hover,
    .btn-delete:hover {
        filter: brightness(0.95);
        color: #fff;
    }

    /* ===== Tabla ===== */
    table.table th,
    table.table td {
        vertical-align: middle;
    }

    thead.bg-primary th {
        color: #fff;
    }

    .badge {
        font-weight: 600;
    }

    /* ===== Responsive acciones ===== */
    @media (max-width: 576px) {
        .actions-flex {
            gap: .35rem !important;
        }

        .actions-flex .btn {
            padding: .3rem .5rem;
        }
    }
</style>
@endpush

@section('content')
{{-- Botón: ordenar alfabéticamente --}}
<form action="{{ route('admin.hotels.sort') }}" method="POST" class="mb-3">
    @csrf
    <button type="submit" class="btn btn-view">
        <i class="fas fa-sort-alpha-down me-1"></i>
        {{ __('pickups.hotels.sort_alpha') }}
    </button>
</form>

{{-- Crear hotel --}}
@can('create-hotels')
<form action="{{ route('admin.hotels.store') }}" method="POST" class="mb-4" autocomplete="off" novalidate>
    @csrf
    <div class="row g-2 align-items-end">
        <div class="col-md-6">
            <label class="form-label" for="hotelNameCreate">{{ __('pickups.hotels.name') }}</label>
            <input
                id="hotelNameCreate"
                type="text"
                name="name"
                class="form-control @error('name') is-invalid @enderror"
                placeholder="{{ __('pickups.hotels.name_placeholder') }}"
                value="{{ old('name') }}"
                @if ($errors->has('name')) autofocus @endif
            required
            >
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-2">
            <button class="btn btn-edit w-100" type="submit">
                <i class="fas fa-plus me-1"></i>
                {{ __('pickups.hotels.add') }}
            </button>
        </div>
    </div>
</form>
@endcan

{{-- Tabla --}}
<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover align-middle">
        <thead class="bg-primary">
            <tr class="text-center">
                <th>{{ __('pickups.hotels.name') }}</th>
                <th style="width:140px">{{ __('pickups.hotels.status') }}</th>
                <th style="width:260px">{{ __('pickups.hotels.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($hotels as $hotel)
            <tr>
                <td class="fw-semibold">
                    <i class="fas fa-bed text-muted me-1"></i>
                    {{ $hotel->name }}
                </td>
                <td class="text-center">
                    <span class="badge {{ $hotel->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $hotel->is_active ? __('pickups.hotels.active') : __('pickups.hotels.inactive') }}
                    </span>
                </td>
                <td class="text-center">
                    <div class="d-inline-flex flex-wrap actions-flex gap-2">
                        {{-- ✏️ Editar (modal único) --}}
                        @can('edit-hotels')
                        <button
                            class="btn btn-edit btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#editHotelModal"
                            data-id="{{ $hotel->hotel_id }}"
                            data-name="{{ $hotel->name }}"
                            data-active="{{ $hotel->is_active ? 1 : 0 }}"
                            title="{{ __('pickups.hotels.edit') }}"
                            aria-label="{{ __('pickups.hotels.edit') }}: {{ $hotel->name }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        @endcan

                        {{-- 🔁 Activar/Desactivar --}}
                        @can('edit-hotels')
                        <form action="{{ route('admin.hotels.toggle', $hotel->hotel_id) }}"
                            method="POST"
                            class="d-inline toggle-form"
                            data-name="{{ $hotel->name }}"
                            data-active="{{ $hotel->is_active ? 1 : 0 }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="btn btn-toggle btn-sm"
                                title="{{ $hotel->is_active ? __('pickups.hotels.deactivate') : __('pickups.hotels.activate') }}"
                                aria-label="{{ $hotel->is_active ? __('pickups.hotels.deactivate') : __('pickups.hotels.activate') }}">
                                <i class="fas fa-toggle-{{ $hotel->is_active ? 'on' : 'off' }}"></i>
                            </button>
                        </form>
                        @endcan

                        {{-- 🗑️ Eliminar definitivo --}}
                        @can('delete-hotels')
                        <form action="{{ route('admin.hotels.destroy', $hotel->hotel_id) }}"
                            method="POST"
                            class="d-inline delete-form"
                            data-name="{{ $hotel->name }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-delete btn-sm"
                                title="{{ __('pickups.hotels.delete') }}"
                                aria-label="{{ __('pickups.hotels.delete') }}: {{ $hotel->name }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center text-muted py-4">
                    <i class="fas fa-info-circle me-1"></i>
                    {{ __('pickups.hotels.no_records') }}
                </td>
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
                <h5 class="modal-title">
                    <i class="fas fa-edit me-1"></i>
                    {{ __('pickups.hotels.edit_title') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('pickups.hotels.close') }}"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="hotelNameInput">{{ __('pickups.hotels.name') }}</label>
                    <input
                        type="text"
                        name="name"
                        id="hotelNameInput"
                        class="form-control"
                        placeholder="{{ __('pickups.hotels.name_placeholder') }}"
                        required>
                </div>
                <input type="hidden" name="is_active" value="0">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="hotelActiveInput" name="is_active" value="1">
                    <label class="form-check-label" for="hotelActiveInput">{{ __('pickups.hotels.active') }}</label>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-edit">
                    <i class="fas fa-save me-1"></i>
                    {{ __('pickups.hotels.save_changes') }}
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ __('pickups.hotels.cancel') }}
                </button>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    /* =========================
   i18n strings para JS
   ========================= */
    const I18N = {
        // Confirmaciones (toggle activación)
        confirmActivateTitle: @json(__('pickups.hotels.confirm_activate_title')),
        confirmActivateText: @json(__('pickups.hotels.confirm_activate_text')),
        confirmDeactivateTitle: @json(__('pickups.hotels.confirm_deactivate_title')),
        confirmDeactivateText: @json(__('pickups.hotels.confirm_deactivate_text')),

        // Confirmación eliminar
        confirmDeleteTitle: @json(__('pickups.hotels.confirm_delete_title')),
        confirmDeleteText: @json(__('pickups.hotels.confirm_delete_text')),

        // Botones genéricos
        confirmBtn: @json(__('pickups.hotels.save_changes')),
        cancel: @json(__('pickups.hotels.cancel')),
        errorTitle: @json(__('pickups.hotels.error_title')),
    };

    // Reemplazar :name en textos
    const withName = (tpl, name) => (tpl || '').replace(':name', name || '');

    // ---- Rellena el modal de edición ----
    document.getElementById('editHotelModal')?.addEventListener('show.bs.modal', function(ev) {
        const btn = ev.relatedTarget;
        if (!btn) return;

        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name') || '';
        const active = btn.getAttribute('data-active') === '1';

        const form = document.getElementById('editHotelForm');
        form.action = "{{ route('admin.hotels.update', '__ID__') }}".replace('__ID__', id);

        document.getElementById('hotelNameInput').value = name;
        document.getElementById('hotelActiveInput').checked = active;
    });

    // ---- Confirmación Activar/Desactivar ----
    document.querySelectorAll('.toggle-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const name = form.getAttribute('data-name') || '';
            const isActive = form.getAttribute('data-active') === '1';

            const title = isActive ? I18N.confirmDeactivateTitle : I18N.confirmActivateTitle;
            const text = isActive ? I18N.confirmDeactivateText : I18N.confirmActivateText;

            Swal.fire({
                title: withName(title, name),
                text: withName(text, name),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: I18N.confirmBtn,
                cancelButtonText: I18N.cancel
            }).then((result) => {
                if (result.isConfirmed) form.submit();
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
                text: withName(I18N.confirmDeleteText, name),
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: I18N.confirmBtn,
                cancelButtonText: I18N.cancel
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    // ---- Mensajes del controller ----
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: @json(__('pickups.hotels.title')),
        text: @json(session('success')),
        timer: 2500,
        showConfirmButton: false
    });
    @endif

    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: I18N.errorTitle,
        text: @json(session('error')),
        timer: 2600,
        showConfirmButton: false
    });
    @endif

    // ---- Errores de validación ----
    @if($errors-> any())
    Swal.fire({
        icon: 'error',
        title: I18N.errorTitle,
        text: @json($errors-> first()),
    });
    @endif
</script>
@stop
