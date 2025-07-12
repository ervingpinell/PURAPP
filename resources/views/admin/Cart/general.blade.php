@extends('adminlte::page')

@section('title', 'Carritos de Todos los Clientes')

@section('content_header')
    <h1><i class="fas fa-shopping-cart"></i> Carritos de Todos los Clientes</h1>
@stop

@section('content')
{{-- ✅ Filtros --}}
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
        <strong><i class="fas fa-filter"></i> Filtros</strong>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Correo del Cliente</label>
                <input type="text" name="correo" class="form-control" placeholder="cliente@correo.com" value="{{ request('correo') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-control">
                    <option value="">-- Todos --</option>
                    <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
                <a href="{{ route('admin.cart.general') }}" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ✅ Tabla de carritos --}}
@if($carritos->count())
    <div class="table-responsive">
        <table class="table table-bordered table-hover shadow-sm">
            <thead class="table-dark">
                <tr class="text-center align-middle">
                    <th>Cliente</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Tour</th>
                    <th>Fecha</th>
                    <th>Horario</th>
                    <th>Idioma</th>
                    <th>Adultos</th>
                    <th>Niños</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($carritos as $cart)
                    @foreach($cart->items as $item)
                        <tr class="align-middle text-center">
                            <td><strong>{{ $cart->user->full_name }}</strong></td>
                            <td>{{ $cart->user->email }}</td>
                            <td>{{ $cart->user->phone }}</td>
                            <td>{{ $item->tour->name }}</td>
                            <td>{{ $item->tour_date }}</td>
                            <td>
                                @if($item->schedule)
                                    <span class="badge bg-success">
                                        {{ \Carbon\Carbon::parse($item->schedule->start_time)->format('g:i A') }}
                                        –
                                        {{ \Carbon\Carbon::parse($item->schedule->end_time)->format('g:i A') }}
                                    </span>
                                @else
                                    <span class="text-muted">Sin horario</span>
                                @endif
                            </td>
                            <td>{{ $item->language->name }}</td>
                            <td>{{ $item->adults_quantity }}</td>
                            <td>{{ $item->kids_quantity }}</td>
                            <td><strong>₡{{ number_format(($item->adult_price * $item->adults_quantity) + ($item->kid_price * $item->kids_quantity), 2) }}</strong></td>
                            <td>
                                <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-danger' }}">
                                    <i class="fas {{ $item->is_active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                    {{ $item->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="d-flex justify-content-center gap-1">
                                {{-- Editar --}}
                                <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $item->item_id }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- Eliminar --}}
                                <form action="{{ route('admin.cart.item.destroy', $item->item_id) }}" method="POST" class="d-inline-block form-eliminar">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ✅ Modales de edición --}}
    @foreach($carritos as $cart)
        @foreach($cart->items as $item)
            <div class="modal fade" id="modalEditar{{ $item->item_id }}" tabindex="-1" aria-labelledby="modalLabel{{ $item->item_id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('admin.cart.updateFromPost', $item->item_id) }}" class="modal-content">
                        @csrf
                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title" id="modalLabel{{ $item->item_id }}">Editar Ítem del Carrito</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Fecha del Tour</label>
                                <input type="date" name="tour_date" class="form-control" value="{{ $item->tour_date }}" required>
                            </div>
                            <div class="mb-3">
                                <label>Cantidad de Adultos</label>
                                <input type="number" name="adults_quantity" class="form-control" value="{{ $item->adults_quantity }}" min="1" required>
                            </div>
                            <div class="mb-3">
                                <label>Cantidad de Niños</label>
                                <input type="number" name="kids_quantity" class="form-control" value="{{ $item->kids_quantity }}" min="0" max="2">
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="check{{ $item->item_id }}" {{ $item->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="check{{ $item->item_id }}">
                                    Reserva activa
                                </label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endforeach

@else
    <div class="alert alert-info text-center">
        <i class="fas fa-info-circle"></i> No hay registros que coincidan con los filtros aplicados.
    </div>
@endif
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '{{ session("success") }}',
                showConfirmButton: false,
                timer: 2000
            });
        </script>
    @endif

    <script>
        document.querySelectorAll('.form-eliminar').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Eliminar este ítem del carrito?',
                    text: "Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@stop
