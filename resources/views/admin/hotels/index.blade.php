@extends('adminlte::page')

@section('title', 'Lista de Hoteles')

@section('content_header')
    <h1>Hoteles Registrados</h1>
@stop

@section('content')
    {{-- Botón para ordenar --}}
    <form action="{{ route('admin.hotels.sort') }}" method="POST" class="mb-3">
        @csrf
        <button type="submit" class="btn btn-view">
            <i class="fas fa-sort-alpha-down"></i> Ordenar alfabéticamente
        </button>
    </form>

    {{-- Crear hotel --}}
    <form action="{{ route('admin.hotels.store') }}" method="POST" class="mb-4" autocomplete="off">
        @csrf
        <div class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Nombre del hotel</label>
                <input
                    type="text"
                    name="name"
                    class="form-control"
                    placeholder="Ej.: Hotel Arenal Springs"
                    value="{{ old('name') }}"
                    required
                >
            </div>
            <div class="col-md-2">
                <button class="btn btn-edit w-100" type="submit">
                    <i class="fas fa-plus"></i> Agregar
                </button>
            </div>
        </div>
    </form>

    {{-- Tabla --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="bg-primary text-white">
                <tr class="text-center">
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th style="width:260px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($hotels as $hotel)
                    <tr>
                        <td class="fw-semibold">{{ $hotel->name }}</td>
                        <td class="text-center">
                            <span class="badge {{ $hotel->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $hotel->is_active ? 'Activo' : 'Inactivo' }}
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
                                    title="Editar hotel">
                                    <i class="fas fa-edit"></i>
                                </button>

                                {{-- 🔁 Activar/Desactivar --}}
                                <form action="{{ route('admin.hotels.toggle', $hotel->hotel_id) }}"
                                      method="POST" class="d-inline toggle-form">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="btn btn-toggle btn-sm"
                                        title="{{ $hotel->is_active ? 'Desactivar' : 'Activar' }}">
                                        <i class="fas fa-toggle-{{ $hotel->is_active ? 'on' : 'off' }}"></i>
                                    </button>
                                </form>

                                {{-- 🗑️ Eliminar definitivo --}}
                                <form action="{{ route('admin.hotels.destroy', $hotel->hotel_id) }}"
                                      method="POST" class="d-inline delete-form">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-delete btn-sm" title="Eliminar hotel">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">No hay hoteles registrados.</td>
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
                    <h5 class="modal-title">Editar hotel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input
                            type="text"
                            name="name"
                            id="hotelNameInput"
                            class="form-control"
                            placeholder="Ej.: Hotel Arenal Springs"
                            required
                        >
                    </div>
                    <input type="hidden" name="is_active" value="0">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="hotelActiveInput" name="is_active" value="1">
                        <label class="form-check-label" for="hotelActiveInput">Activo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-edit">
                        <i class="fas fa-save"></i> Guardar cambios
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Rellena el modal de edición
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

// Confirmación con SweetAlert2
document.querySelectorAll('.toggle-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se cambiará el estado de este hotel.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, cambiar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});

document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: '¿Eliminar definitivamente?',
            text: "No podrás revertir esta acción.",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});

// SweetAlert para mensajes de éxito o error
@if (session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: '{{ session('success') }}',
        timer: 2500,
        showConfirmButton: false
    });
@endif

@if (session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('error') }}',
        timer: 2500,
        showConfirmButton: false
    });
@endif
</script>
@stop
