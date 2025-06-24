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
                        <th>Hotel</th>
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
                            <td>
                                @if($item->is_other_hotel)
                                    {{ $item->other_hotel_name }}
                                @else
                                    {{ optional($item->hotel)->name ?? '—' }}
                                @endif
                            </td>
                            <td>{{ $item->adults_quantity }}</td>
                            <td>{{ $item->kids_quantity }}</td>
                            <td>
                              ${{ number_format(
                                  $item->tour->adult_price * $item->adults_quantity +
                                  $item->tour->kid_price   * $item->kids_quantity,
                                  2
                              ) }}
                            </td>
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

        {{-- Boton de Enviar Reserva --}}
        <form method="POST" action="{{ route('admin.reservas.storeFromCart') }}">
            @csrf
            <button type="submit" class="btn btn-success btn-lg mt-3">
                <i class="fas fa-paper-plane"></i> Confirmar y Enviar Solicitud de Reserva
            </button>
        </form>

        {{-- Modales de edición --}}
        @foreach($cart->items as $item)
            <div class="modal fade" id="modalEditar{{ $item->item_id }}" tabindex="-1" aria-labelledby="modalLabel{{ $item->item_id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('admin.cart.update', $item->item_id) }}" class="modal-content">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="is_active" value="0">

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
                                <label>Hotel</label>
                                <select name="hotel_id"
                                        id="edit_hotel_{{ $item->item_id }}"
                                        class="form-control">
                                    <option value="">Seleccione un hotel</option>
                                    @foreach($hotels as $hotel)
                                        <option value="{{ $hotel->hotel_id }}"
                                            {{ !$item->is_other_hotel && $item->hotel_id == $hotel->hotel_id ? 'selected':'' }}>
                                            {{ $hotel->name }}
                                        </option>
                                    @endforeach
                                    <option value="other" {{ $item->is_other_hotel ? 'selected':'' }}>
                                        Otro…
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3 {{ $item->is_other_hotel ? '' : 'd-none' }}"
                                 id="edit_other_container_{{ $item->item_id }}">
                                <label>Nombre de hotel</label>
                                <input type="text"
                                       name="other_hotel_name"
                                       class="form-control"
                                       value="{{ $item->other_hotel_name }}">
                            </div>
                            <input type="hidden"
                                   name="is_other_hotel"
                                   id="edit_is_other_{{ $item->item_id }}"
                                   value="{{ $item->is_other_hotel ? 1 : 0 }}">

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
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Confirmación al eliminar ítem
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', e => {
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
                }).then(result => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });

        // Toasts de sesión
        @if(session('success'))
            Swal.fire({ icon: 'success', title: '{{ session("success") }}', timer:2000, showConfirmButton:false });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: '¡Ups!', text: @js(session('error')), confirmButtonText:'Entendido' });
        @endif

        // Control Hotel “Otro…”
        document.addEventListener('DOMContentLoaded', () => {
            // En el formulario de confirmar carrito
            const hotelSel = document.getElementById('hotel_id'),
                  wrap     = document.getElementById('other_hotel_wrapper'),
                  hidden   = document.getElementById('is_other_hotel'),
                  input    = document.getElementById('other_hotel_name');

            hotelSel.addEventListener('change', () => {
                if (hotelSel.value === 'other') {
                    wrap.classList.remove('d-none');
                    hidden.value = 1;
                } else {
                    wrap.classList.add('d-none');
                    hidden.value = 0;
                    input.value  = '';
                }
            });

            // En cada modal de edición
            @foreach($cart->items as $item)
                (function(){
                    const sel    = document.getElementById('edit_hotel_{{ $item->item_id }}'),
                          cont   = document.getElementById('edit_other_container_{{ $item->item_id }}'),
                          hid    = document.getElementById('edit_is_other_{{ $item->item_id }}');

                    sel.addEventListener('change', () => {
                        if (sel.value === 'other') {
                            cont.classList.remove('d-none');
                            hid.value = 1;
                        } else {
                            cont.classList.add('d-none');
                            cont.querySelector('input').value = '';
                            hid.value = 0;
                        }
                    });
                })();
            @endforeach
        });
    </script>
@stop
