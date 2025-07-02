@extends('adminlte::page')

@section('title', 'Reservas')

@section('content_header')

    <h1>Gestión de Reservas</h1>
@stop

@section('content')

<div class="p-3 table-responsive" >
    <div class="container-fluid mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <a href="#" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
                    <i class="fas fa-plus"></i> Añadir Reserva
                </a>
                <a href="{{ route('admin.reservas.pdf') }}" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Descargar PDF
                </a>
            </div>
            <form method="GET" class="d-flex align-items-end gx-2">
                <div class="form-group mb-0">
                    <label for="reference" class="form-label visually-hidden">Referencia</label>
                    <input
                        type="text"
                        id="reference"
                        name="reference"
                        value="{{ request('reference') }}"
                        class="form-control"
                        placeholder="Ej: ABC123XYZ"
                    >
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </form>
        </div>
    </div>

    <table class="table table-striped table-bordered table-hover">
        <thead class="bg-primary text-white">
            <thead class="bg-primary text-white">
            <tr>
              <th>ID Reserva</th>
              <th>Cliente</th>
              <th>Correo</th>
              <th>Teléfono</th>
              <th>Tour</th>
              <th>Fecha Reserva</th>
              <th>Fecha Tour</th>
              <th>Hotel</th>
              <th>Horarios</th>
              <th>Tipo</th>
              <th>Adultos</th>
              <th>Niños</th>
              <th>Estado</th>
              <th>Referencia</th>
              <th>Total</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
          @foreach($bookings as $reserva)
            @php
              $detail = $reserva->detail;
              $tour   = $detail->tour;
            @endphp
            <tr>
              <td>{{ $reserva->booking_id }}</td>
              <td>{{ $reserva->user->full_name ?? '-' }}</td>
              <td>{{ $reserva->user->email     ?? '-' }}</td>
              <td>{{ $reserva->user->phone     ?? '-' }}</td>
              <td>{{ $tour->name              ?? '-' }}</td>
              <td>{{ \Carbon\Carbon::parse($reserva->booking_date)->format('d/m/Y') }}</td>
              <td>{{ \Carbon\Carbon::parse($detail->tour_date)->format('d/m/Y') }}</td>
              <td>
                @if($detail->is_other_hotel)
                  {{ $detail->other_hotel_name }}
                @else
                  {{ optional($detail->hotel)->name ?? '-' }}
                @endif
              </td>
              {{-- Horarios --}}
              <td>
                @forelse($tour->schedules as $sched)
                  <div>
                    <span class="badge bg-success">
                      {{ \Carbon\Carbon::parse($sched->start_time)->format('g:i A') }}
                      &ndash;
                      {{ \Carbon\Carbon::parse($sched->end_time  )->format('g:i A') }}
                    </span>
                  </div>
                @empty
                  <span class="text-muted">Sin horarios</span>
                @endforelse
              </td>
              {{-- Tipo --}}
              <td>{{ optional($tour->tourType)->name ?? '—' }}</td>
              <td>{{ $detail->adults_quantity }}</td>
              <td>{{ $detail->kids_quantity   }}</td>
              <td>{{ ucfirst($reserva->status) }}</td>
              <td>{{ $reserva->booking_reference }}</td>
              <td>${{ number_format($reserva->total, 2) }}</td>
              <td class="text-nowrap">
                {{-- Editar --}}
                <button class="btn btn-warning btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEditar{{ $reserva->booking_id }}">
                  <i class="fas fa-edit"></i>
                </button>
                {{-- Eliminar --}}
                <form action="{{ route('admin.reservas.destroy', $reserva->booking_id) }}"
                      method="POST" style="display:inline">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger btn-sm"
                          onclick="return confirm('¿Eliminar esta reserva?')">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </form>
                {{-- Comprobante --}}
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
                                <label class="form-label">Cantidad Adultos</label>
                                <input
                                    type="number"
                                    name="adults_quantity"
                                    class="form-control cantidad-adultos"
                                    value="{{ optional($reserva->detail)->adults_quantity ?? 0 }}"
                                    min="1"
                                    required
                                >
                                <label label class="form-label">Cantidad Niños</label>
                                <input
                                    type="number"
                                    name="kids_quantity"
                                    class="form-control cantidad-ninos"
                                    value="{{ optional($reserva->detail)->kids_quantity ?? 0 }}"
                                    min="0" max="2"
                                    required
                                >
                                <div class="mb-3">
                                    <label class="form-label">Hotel</label>
                                    <select name="hotel_id"
                                            id="edit_hotel_{{ $reserva->booking_id }}"
                                            class="form-control">
                                        <option value="">Seleccione un hotel</option>
                                        @foreach($hotels as $h)
                                            <option value="{{ $h->hotel_id }}"
                                                {{ !$reserva->detail->is_other_hotel && $reserva->detail->hotel_id == $h->hotel_id ? 'selected' : '' }}>
                                                {{ $h->name }}
                                            </option>
                                        @endforeach
                                        <option value="other" {{ $reserva->detail->is_other_hotel ? 'selected':'' }}>
                                            Otro…
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-3 {{ $reserva->detail->is_other_hotel ? '' : 'd-none' }}"
                                     id="edit_other_hotel_container_{{ $reserva->booking_id }}">
                                    <label class="form-label">Nombre de hotel</label>
                                    <input type="text"
                                           name="other_hotel_name"
                                           class="form-control"
                                           value="{{ $reserva->detail->other_hotel_name }}">
                                </div>

                                <input type="hidden"
                                       name="is_other_hotel"
                                       id="edit_is_other_hotel_{{ $reserva->booking_id }}"
                                       value="{{ $reserva->detail->is_other_hotel ? 1 : 0 }}">

                                <label class="form-label">Precio Adulto</label>
                                <input
                                    type="text"
                                    class="form-control precio-adulto"
                                    value="{{ number_format(optional($reserva->detail)->adult_price ?? 0, 2) }}"
                                    readonly
                                >

                                <label class="form-label">Precio Niño</label>
                                <input
                                    type="text"
                                    class="form-control precio-nino"
                                    value="{{ number_format(optional($reserva->detail)->kid_price ?? 0, 2) }}"
                                    readonly
                                >

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
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
        </tbody>
    </table>

    {{-- Paginación general debajo de la tabla --}}
    @if($bookings->hasPages())
      <nav class="d-flex justify-content-center mt-3">
        <ul class="pagination pagination-sm">
          {{-- Previous --}}
          <li class="page-item {{ $bookings->onFirstPage() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $bookings->previousPageUrl() }}" tabindex="-1">Anterior</a>
          </li>

          {{-- Current / Total --}}
          <li class="page-item disabled">
            <span class="page-link">{{ $bookings->currentPage() }} / {{ $bookings->lastPage() }}</span>
          </li>

          {{-- Next --}}
          <li class="page-item {{ $bookings->hasMorePages() ? '' : 'disabled' }}">
            <a class="page-link" href="{{ $bookings->nextPageUrl() }}">Siguiente</a>
          </li>
        </ul>
      </nav>
    @endif

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
          {{-- Cliente --}}
          <div class="mb-3">
            <label class="form-label">Cliente</label>
            <select name="user_id" class="form-control" required>
              @foreach(\App\Models\User::all() as $u)
                <option value="{{ $u->user_id }}">{{ $u->full_name }}</option>
              @endforeach
            </select>
          </div>

          {{-- Tour --}}
          <div class="mb-3">
            <label class="form-label">Tour</label>
            <select name="tour_id"
                    id="selectTour"
                    class="form-control"
                    required>
              <option value="">Seleccione un tour</option>
              @foreach(\App\Models\Tour::all() as $t)
                <option value="{{ $t->tour_id }}"
                        data-precio-adulto="{{ $t->adult_price }}"
                        data-precio-nino="{{ $t->kid_price }}">
                  {{ $t->name }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Idioma --}}
          <div class="mb-3">
            <label class="form-label">Idioma</label>
            <select name="tour_language_id" class="form-control" required>
              @foreach(\App\Models\TourLanguage::all() as $lang)
                <option value="{{ $lang->tour_language_id }}">{{ $lang->name }}</option>
              @endforeach
            </select>
          </div>

          {{-- Fecha Reserva --}}
          <div class="mb-3">
            <label class="form-label">Fecha Reserva</label>
            <input type="date" name="booking_date" class="form-control" required>
          </div>

          {{-- Fecha del Tour --}}
          <div class="mb-3">
            <label class="form-label">Fecha del Tour</label>
            <input type="date" name="tour_date" class="form-control" required>
          </div>

          {{-- Hotel --}}
          <div class="mb-3">
            <label class="form-label">Hotel</label>
            <select name="hotel_id" class="form-control" required>
              <option value="">Seleccione un hotel</option>
              @foreach($hotels as $h)
                <option value="{{ $h->hotel_id }}">{{ $h->name }}</option>
              @endforeach
              <option value="other">Otro…</option>
            </select>
          </div>

          <div class="mb-3 d-none" id="otherHotelRegistrarWrapper">
            <label class="form-label">Nombre de otro hotel</label>
            <input type="text" name="other_hotel_name" class="form-control" placeholder="Escriba el nombre del hotel">
          </div>
          <input type="hidden" name="is_other_hotel" id="isOtherHotelRegistrar" value="0">

          {{-- Cantidad Adultos --}}
          <div class="mb-3">
            <label class="form-label">Adultos</label>
            <input type="number" name="adults_quantity" class="form-control cantidad-adultos" min="1" required>
          </div>

          {{-- Cantidad Niños --}}
          <div class="mb-3">
            <label class="form-label">Niños</label>
            <input type="number" name="kids_quantity" class="form-control cantidad-ninos" min="0" max="2" required>
          </div>

          {{-- Precio Adulto --}}
          <div class="mb-3">
            <label class="form-label">Precio Adulto</label>
            <input type="text" class="form-control precio-adulto" readonly>
          </div>

          {{-- Precio Niño --}}
          <div class="mb-3">
            <label class="form-label">Precio Niño</label>
            <input type="text" class="form-control precio-nino" readonly>
          </div>

          {{-- Total --}}
          <div class="mb-3">
            <label class="form-label">Total a Pagar</label>
            <input type="text" name="total" class="form-control total-pago" readonly>
          </div>

          {{-- Estado --}}
          <div class="mb-3">
            <label class="form-label">Estado</label>
            <select name="status" class="form-control" required>
              <option value="pending">Pending</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
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
<link rel="stylesheet" href="{{ asset('css/calendar.css') }}">
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
  
  @foreach($bookings as $reserva)
  (function(){
    const sel    = document.getElementById('edit_hotel_{{ $reserva->booking_id }}');
    const wrap   = document.getElementById('edit_other_hotel_container_{{ $reserva->booking_id }}');
    const hidden = document.getElementById('edit_is_other_hotel_{{ $reserva->booking_id }}');

    sel.addEventListener('change', () => {
      if (sel.value === 'other') {
        wrap.classList.remove('d-none');
        hidden.value = 1;
      } else {
        wrap.classList.add('d-none');
        wrap.querySelector('input').value = '';
        hidden.value = 0;
      }
    });
  })();
@endforeach

</script>
@if($errors->has('capacity'))
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      Swal.fire({
        icon: 'error',
        title: 'Cupo Excedido',
        text: @json($errors->first('capacity')),
        confirmButtonColor: '#d33'
      });
    });
  </script>
@endif

@if(session('showEditModal'))
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const id = '{{ session('showEditModal') }}';
      const modal = new bootstrap.Modal(document.getElementById('modalEditar' + id));
      modal.show();
    });
  </script>
@endif
@stop