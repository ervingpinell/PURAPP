@extends('adminlte::page')

@section('title', 'Mi Carrito')

@section('content_header')
    <h1><i class="fas fa-shopping-cart"></i> Carrito de Reservas</h1>
@stop

@section('content')
    {{-- Filtros --}}
    <form method="GET" class="mb-4 d-flex justify-content-center align-items-center gap-2">
        <label class="mb-0"><i class="fas fa-filter"></i> Estado:</label>
        <select name="estado" class="form-control w-auto">
            <option value="">-- Todos --</option>
            <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Pendientes</option>
            <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Canceladas</option>
        </select>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Filtrar
        </button>
    </form>

    @if($cart && $cart->items->count())
        {{-- Información del cliente --}}
        <div class="card mb-4 shadow">
            <div class="card-header bg-info text-white">
                <i class="fas fa-user"></i> Información del Cliente
            </div>
            <div class="card-body">
                <p><i class="fas fa-id-card"></i> <strong>Nombre:</strong> {{ $cart->user->full_name ?? 'N/A' }}</p>
                <p><i class="fas fa-envelope"></i> <strong>Email:</strong> {{ $cart->user->email ?? 'N/A' }}</p>
                <p><i class="fas fa-phone"></i> <strong>Teléfono:</strong> {{ $cart->user->phone ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="table-responsive">
            <table class="table table-bordered table-hover shadow-sm">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Tour</th>
                        <th>Fecha</th>
                        <th>Idioma</th>
                        <th>Adultos</th>
                        <th>Niños</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-center align-middle">
                    @foreach($cart->items as $item)
                        <tr>
                            <td>{{ $item->tour->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tour_date)->format('d/m/Y') }}</td>
                            <td>{{ $item->language->name }}</td>
                            <td>{{ $item->adults_quantity }}</td>
                            <td>{{ $item->kids_quantity }}</td>
                            <td>₡{{ number_format(($item->adult_price * $item->adults_quantity) + ($item->kid_price * $item->kids_quantity), 2) }}</td>
                            <td>
                                @if($item->is_active)
                                    <span class="badge bg-success"><i class="fas fa-check-circle"></i> Activo</span>
                                @else
                                    <span class="badge bg-secondary"><i class="fas fa-times-circle"></i> Inactivo</span>
                                @endif
                            </td>
                            <td>
                                {{-- Editar --}}
                                <button class="btn btn-sm btn-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditar{{ $item->item_id }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                {{-- Eliminar --}}
                                <form method="POST"
                                      action="{{ route('admin.cart.item.destroy', $item->item_id) }}"
                                      class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Confirmar toda la reserva --}}
        <form method="POST" action="{{ route('admin.reservas.storeFromCart') }}">
            @csrf
            <button type="submit" class="btn btn-success btn-lg mt-3">
                <i class="fas fa-paper-plane"></i> Confirmar y Enviar Solicitud de Reserva
            </button>
        </form>

        {{-- Modales de edición (fuera de la tabla) --}}
        @foreach($cart->items as $item)
            <div class="modal fade" id="modalEditar{{ $item->item_id }}" tabindex="-1" aria-labelledby="modalLabel{{ $item->item_id }}" aria-hidden="true">
                <div class="modal-dialog">
                <form method="POST"
                        action="{{ route('admin.cart.update', $item->item_id) }}"
                        class="modal-content">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="is_active" value="0">

                    <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="modalLabel{{ $item->item_id }}">Editar Ítem del Carrito</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                    {{-- Fecha --}}
                    <div class="mb-3">
                        <label for="date{{ $item->item_id }}">Fecha del Tour</label>
                        <input id="date{{ $item->item_id }}"
                            type="date"
                            name="tour_date"
                            class="form-control"
                            value="{{ $item->tour_date }}"
                            required>
                    </div>

                    {{-- Idioma --}}
                    <div class="mb-3">
                        <label for="language{{ $item->item_id }}">Idioma</label>
                        <select id="language{{ $item->item_id }}"
                                name="language_id"
                                class="form-control">
                        @foreach($languages as $lang)
                            <option value="{{ $lang->id }}"
                            {{ $item->language_id == $lang->id ? 'selected' : '' }}>
                            {{ $lang->name }}
                            </option>
                        @endforeach
                        </select>
                    </div>

                    {{-- Adultos --}}
                    <div class="mb-3">
                        <label for="adults{{ $item->item_id }}">Cantidad de Adultos</label>
                        <input id="adults{{ $item->item_id }}"
                            type="number"
                            name="adults_quantity"
                            class="form-control"
                            value="{{ $item->adults_quantity }}"
                            min="1"
                            required>
                    </div>

                    {{-- Niños --}}
                    <div class="mb-3">
                        <label for="kids{{ $item->item_id }}">Cantidad de Niños</label>
                        <input id="kids{{ $item->item_id }}"
                            type="number"
                            name="kids_quantity"
                            class="form-control"
                            value="{{ $item->kids_quantity }}"
                            min="0">
                    </div>

                    {{-- Estado activo/inactivo --}}
                    <div class="form-check mb-3">
                        <input class="form-check-input"
                            type="checkbox"
                            name="is_active"
                            value="1"
                            id="check{{ $item->item_id }}"
                            {{ $item->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="check{{ $item->item_id }}">
                        Reserva activa
                        </label>
                    </div>
                    </div>

                    <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    </div>
                </form>
                </div>
            </div>
            @endforeach


    @else
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle"></i> Tu carrito está vacío.
        </div>
    @endif
@stop

@section('js')
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>

    <script>
        // Confirmación de eliminación
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Eliminar este ítem?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Debug de modales
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('shown.bs.modal', () => {
                console.log('Modal abierto:', modal.id);
            });
        });
    </script>

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

@stop
