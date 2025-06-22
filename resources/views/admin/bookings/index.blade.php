@extends('adminlte::page')

@section('title', 'Reservas')

@section('content_header')
    <h1>Gestión de Reservas</h1>
@stop

@section('content')
<div class="p-3 table-responsive">
    <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
        <i class="fas fa-plus"></i> Añadir Reserva
    </a>
    <a href="{{ route('admin.reservas.pdf') }}" class="btn btn-danger mb-3" target="_blank">
        <i class="fas fa-file-pdf"></i> Descargar PDF
    </a>

    <table class="table table-striped table-bordered table-hover">
        <thead class="bg-primary text-white">
            <tr>
                <th>ID Reserva</th>
                <th>Cliente</th>
                <th>Correo</th>
                <th>Tour</th>
                <th>Fecha Reserva</th>
                <th>Estado</th>
                <th>Referencia</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        @foreach($bookings as $reserva)
            <tr>
                <td>{{ $reserva->booking_id }}</td>
                <td>{{ $reserva->user->full_name ?? '-' }}</td>
                <td>{{ $reserva->user->email ?? '-' }}</td>
                <td>{{ $reserva->tour->name    ?? '-' }}</td>
                <td>{{ $reserva->booking_date   }}</td>
                <td>{{ ucfirst($reserva->status) }}</td>
                <td>{{ $reserva->booking_reference }}</td>
                <td>${{ number_format($reserva->total, 2) }}</td>
                <td>
                    {{-- editar --}}
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                            data-bs-target="#modalEditar{{ $reserva->booking_id }}">
                        <i class="fas fa-edit"></i>
                    </button>

                    {{-- eliminar --}}
                    <form action="{{ route('admin.reservas.destroy', $reserva->booking_id) }}"
                          method="POST" style="display:inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm"
                                onclick="return confirm('¿Eliminar esta reserva?')">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>

                    {{-- comprobante --}}
                    <a href="{{ route('admin.reservas.comprobante', $reserva->booking_id) }}"
                       class="btn btn-success btn-sm">
                        <i class="fas fa-file-download"></i>
                    </a>
                </td>
            </tr>

            {{-- Modal Editar --}}
            <div class="modal fade" id="modalEditar{{ $reserva->booking_id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form action="{{ route('admin.reservas.update', $reserva->booking_id) }}"
                          method="POST">
                        @csrf @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Editar Reserva</h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                            {{-- Cantidad Adultos --}}
                            <input
                            type="number"
                            name="adults_quantity"
                            class="form-control cantidad-adultos"
                            value="{{ optional($reserva->detail)->adults_quantity ?? 0 }}"
                            min="1"
                            required
                            >

                            {{-- Cantidad Niños --}}
                            <input
                            type="number"
                            name="kids_quantity"
                            class="form-control cantidad-ninos"
                            value="{{ optional($reserva->detail)->kids_quantity ?? 0 }}"
                            min="0" max="2"
                            required
                            >

                            {{-- Precio Adulto --}}
                            <input
                            type="text"
                            class="form-control precio-adulto"
                            value="{{ number_format(optional($reserva->detail)->adult_price ?? 0, 2) }}"
                            readonly
                            >

                            {{-- Precio Niño --}}
                            <input
                            type="text"
                            class="form-control precio-nino"
                            value="{{ number_format(optional($reserva->detail)->kid_price ?? 0, 2) }}"
                            readonly
                            >


                            {{-- Total recalculado (readonly) --}}
                            <div class="mb-3">
                                <label class="form-label">Total a Pagar</label>
                                <input
                                type="text"
                                name="total"
                                class="form-control total-pago"
                                value="{{ number_format($reserva->total, 2) }}"
                                readonly
                                >
                            </div>

                            {{-- Estado --}}
                            <div class="mb-3">
                                <label class="form-label">Estado</label>
                                <select name="status" class="form-control" required>
                                <option value="pending"   {{ $reserva->status==='pending'   ? 'selected':'' }}>Pending</option>
                                <option value="cancelled" {{ $reserva->status==='cancelled'? 'selected':'' }}>Cancelled</option>
                                </select>
                            </div>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-warning">Actualizar</button>
                                <button class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
        </tbody>
    </table>
</div>

{{-- Modal Registrar --}}
<div class="modal fade" id="modalRegistrar" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.reservas.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Reserva</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>Cliente</label>
                    <select name="user_id" class="form-control" required>
                        @foreach(\App\Models\User::all() as $u)
                            <option value="{{ $u->user_id }}">{{ $u->full_name }}</option>
                        @endforeach
                    </select>
                    <label class="mt-2">Tour</label>
                    <select name="tour_id" class="form-control" required>
                        @foreach(\App\Models\Tour::all() as $t)
                            <option value="{{ $t->tour_id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                    <label class="mt-2">Fecha Reserva</label>
                    <input type="date" name="booking_date" class="form-control" required>
                    <label class="mt-2">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="pending">Pending</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <label class="mt-2">Adultos</label>
                    <input type="number" name="adults_quantity" class="form-control" min="1" required>
                    <label class="mt-2">Niños</label>
                    <input type="number" name="kids_quantity" class="form-control" min="0" required>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Guardar</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop



@section('css')

@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<!-- Script para calcular el total -->
<script>
    function calcularTotal(modal) {
        const adultos = parseInt(modal.querySelector('.cantidad-adultos')?.value || 0);
        const ninos = parseInt(modal.querySelector('.cantidad-ninos')?.value || 0);
        const precioAdulto = parseFloat(modal.querySelector('.precio-adulto')?.value || 0);
        const precioNino = parseFloat(modal.querySelector('.precio-nino')?.value || 0);

        const total = (adultos * precioAdulto) + (ninos * precioNino);
        const totalInput = modal.querySelector('.total-pago');
        if (totalInput) totalInput.value = total.toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('input', () => calcularTotal(modal));
        });

        // Cargar precios automáticamente al seleccionar un tour
        const selectTour = document.getElementById('selectTour');
        if (selectTour) {
            selectTour.addEventListener('change', function () {
                const selected = this.options[this.selectedIndex];
                const precioAdulto = selected.getAttribute('data-precio-adulto');
                const precioNino = selected.getAttribute('data-precio-nino');

                const modal = this.closest('.modal');
                if (modal) {
                    modal.querySelector('.precio-adulto').value = precioAdulto;
                    modal.querySelector('.precio-nino').value = precioNino;
                    calcularTotal(modal);
                }
            });
        }
    });
</script>



@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: '{{ session('success') }}',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK'
    });
</script>
@endif
@stop

<script>
  function recalcularTotal(modal) {
    const adultos    = +modal.querySelector('.cantidad-adultos').value || 0;
    const ninos      = +modal.querySelector('.cantidad-ninos').value   || 0;
    const precioA    = parseFloat(modal.querySelector('.precio-adulto').value) || 0;
    const precioN    = parseFloat(modal.querySelector('.precio-nino').value)   || 0;
    const total      = (adultos * precioA) + (ninos * precioN);
    modal.querySelector('.total-pago').value = total.toFixed(2);
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.modal').forEach(modal => {
      modal.addEventListener('input', () => recalcularTotal(modal));
    });
  });
</script>
