@extends('adminlte::page')

@section('title', 'Lista de Hoteles')

@section('content_header')
    <h1>Hoteles Registrados</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{-- Ordenar alfabéticamente --}}
    <form action="{{ route('admin.hotels.sort') }}" method="POST" class="mb-3">
        @csrf
        <button type="submit" class="btn btn-primary">
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
                <button class="btn btn-success w-100" type="submit">
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
                                {{-- ✏️ Editar nombre/estado (modal) --}}
                                <button
                                    class="btn btn-success btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editHotelModal-{{ $hotel->hotel_id }}"
                                    title="Editar hotel">
                                    <i class="fas fa-edit"></i>
                                </button>

                                {{-- 🔁 Activar/Desactivar (rápido) --}}
                                <form action="{{ route('admin.hotels.update', $hotel->hotel_id) }}"
                                      method="POST" class="d-inline">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="name" value="{{ $hotel->name }}">
                                    <input type="hidden" name="is_active" value="{{ $hotel->is_active ? 0 : 1 }}">
                                    <button type="submit"
                                        class="btn btn-warning btn-sm"
                                        title="{{ $hotel->is_active ? 'Desactivar' : 'Activar' }}">
                                        <i class="fas fa-toggle-{{ $hotel->is_active ? 'on' : 'off' }}"></i>
                                    </button>
                                </form>

                                {{-- 🗑️ Eliminar/Desactivar definitivo (según tu destroy actual) --}}
                                <form action="{{ route('admin.hotels.destroy', $hotel->hotel_id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Seguro que deseas cambiar el estado de este hotel?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" title="Eliminar / Cambiar estado">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- MODAL: Editar hotel --}}
                    <div class="modal fade" id="editHotelModal-{{ $hotel->hotel_id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('admin.hotels.update', $hotel->hotel_id) }}"
                                  method="POST" class="modal-content" autocomplete="off">
                                @csrf @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title">Editar hotel</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Nombre</label>
                                        <input
                                            type="text"
                                            name="name"
                                            class="form-control"
                                            placeholder="Ej.: Hotel Arenal Springs"
                                            value="{{ old('name', $hotel->name) }}"
                                            required
                                        >
                                    </div>
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="hotel-active-{{ $hotel->hotel_id }}"
                                            name="is_active"
                                            value="1"
                                            {{ old('is_active', $hotel->is_active) ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="hotel-active-{{ $hotel->hotel_id }}">
                                            Activo
                                        </label>
                                    </div>
                                    {{-- Si NO marcas el checkbox, manda 0 --}}
                                    <input type="hidden" name="is_active" value="0">
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-success">
                                        <i class="fas fa-save"></i> Guardar cambios
                                    </button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">No hay hoteles registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@stop

@section('js')
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stop
