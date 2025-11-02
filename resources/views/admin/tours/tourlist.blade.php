{{-- resources/views/admin/tours/tourlist.blade.php --}}
<style>
  :root {
    --tbl-font-size: 1rem;  /* Cambiado a 1rem = 100% por defecto */
    --btn-cell-mult: 2.2;
    --btn-cell-size: calc(var(--tbl-font-size) * var(--btn-cell-mult));
  }

  .table-responsive-custom {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .table-sm td, .table-sm th {
    padding: .5rem;
    font-size: var(--tbl-font-size);
    vertical-align: middle;
  }

  .font-toolbar {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    margin: 0.5rem 0 1rem;
    flex-wrap: wrap;
  }

  .font-toolbar .btn {
    line-height: 1;
    padding: 0.25rem 0.5rem;
  }

  .font-toolbar .size-indicator {
    min-width: 3.5rem;
    text-align: center;
    font-variant-numeric: tabular-nums;
    font-weight: 500;
  }

  .actions-cell {
    min-width: 280px;
  }

  .actions-cell .d-flex {
    gap: .375rem;
  }

  .actions-cell .btn-sm {
    width: var(--btn-cell-size);
    height: var(--btn-cell-size);
    padding: 0 !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    border-radius: .375rem;
    font-size: var(--tbl-font-size);
  }

  .actions-cell .btn-sm i {
    font-size: 1em;
  }

  .schedule-badge {
    display: inline-block;
    margin: 0.15rem;
    white-space: nowrap;
  }

  .price-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.25rem 0;
    border-bottom: 1px solid #e9ecef;
  }

  .price-item:last-child {
    border-bottom: none;
  }

  .price-label {
    font-weight: 500;
    color: #ffffff;
  }

  .price-value {
    font-weight: 600;
    color: #28a745;
  }

  .price-range {
    font-size: 0.75rem;
    color: #ffffff;
    margin-left: 0.5rem;
  }

  .table-striped tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.03);
  }

  @media (max-width: 992px) {
    :root {
      --btn-cell-mult: 2.0;
    }
  }

  @media (max-width: 768px) {
    :root {
      --btn-cell-mult: 1.8;
    }

    .actions-cell {
      min-width: 240px;
    }
  }

  .table-responsive-custom::-webkit-scrollbar {
    height: 8px;
  }

  .table-responsive-custom::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
  }

  .table-responsive-custom::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
  }

  .table-responsive-custom::-webkit-scrollbar-thumb:hover {
    background: #555;
  }

  .scroll-hint {
    display: none;
    text-align: center;
    padding: 0.5rem;
    background: #e3f2fd;
    border-radius: 0.25rem;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    color: #1976d2;
  }

  @media (max-width: 992px) {
    .scroll-hint {
      display: block;
    }
  }
</style>

@include('admin.carts.cartmodal')

{{-- Toolbar de tamaño de fuente --}}
<div class="font-toolbar">
  <button class="btn btn-outline-secondary btn-sm" id="fontSmaller" type="button"
    title="Disminuir tamaño" aria-label="Disminuir tamaño de fuente">
    A−
  </button>
  <div class="size-indicator" id="fontIndicator" aria-live="polite">100%</div>
  <button class="btn btn-outline-secondary btn-sm" id="fontBigger" type="button"
    title="Aumentar tamaño" aria-label="Aumentar tamaño de fuente">
    A+
  </button>
</div>

{{-- Indicador de scroll --}}
<div class="scroll-hint">
  <i class="fas fa-arrows-alt-h me-1"></i>
  Desliza horizontalmente para ver más columnas
</div>

<div class="table-responsive-custom">
  <table class="table table-sm table-bordered table-striped table-hover" id="toursTable">
    <thead class="bg-primary text-white">
      <tr>
        <th style="min-width: 60px;">ID</th>
        <th style="min-width: 180px;">Nombre</th>
        <th style="min-width: 150px;">Horarios</th>
        <th style="min-width: 180px;">Precios</th>
        <th style="min-width: 100px;">Capacidad</th>
        <th style="min-width: 90px;">Estado</th>
        <th style="min-width: 280px;">Acciones</th>
      </tr>
    </thead>
    <tbody id="toursTbody">
      @foreach($tours as $tour)
        <tr>
          {{-- ID --}}
          <td>{{ $tour->tour_id }}</td>

          {{-- Nombre --}}
          <td>
            <strong>{{ $tour->name }}</strong>
            @if($tour->tourType)
              <br>
              <small class="text-muted">{{ $tour->tourType->name }}</small>
            @endif
          </td>

          {{-- Horarios --}}
          <td>
            @forelse($tour->schedules->sortBy('start_time') as $schedule)
              <span class="badge bg-success schedule-badge">
                {{ date('g:i A', strtotime($schedule->start_time)) }}
              </span>
            @empty
              <span class="text-muted">Sin horarios</span>
            @endforelse
          </td>

          {{-- Precios Dinámicos - SOLO LAS ACTIVAS --}}
          <td>
            @php
              // Filtrar solo precios activos con categorías activas
              $activePrices = $tour->prices
                ->filter(function($price) {
                  return $price->is_active &&
                         $price->category &&
                         $price->category->is_active;
                })
                ->sortBy('category.order');
            @endphp

            @if($activePrices->isNotEmpty())
              @foreach($activePrices as $price)
                <div class="price-item">
                  <span class="price-label">
                    {{ $price->category->name }}
                    <span class="price-range">({{ $price->min_quantity }}-{{ $price->max_quantity }})</span>
                  </span>
                  <span class="price-value">${{ number_format($price->price, 2) }}</span>
                </div>
              @endforeach
            @else
              <span class="text-muted">Sin precios configurados</span>
            @endif
          </td>

          {{-- Capacidad Máxima --}}
          <td class="text-center">
            <span class="badge bg-info">
              {{ $tour->max_capacity }} personas
            </span>
          </td>

          {{-- Estado --}}
          <td class="text-center">
            <span class="badge {{ $tour->is_active ? 'bg-success' : 'bg-secondary' }}">
              {{ $tour->is_active ? 'Activo' : 'Inactivo' }}
            </span>
          </td>

          {{-- Acciones --}}
          <td class="actions-cell">
            @php
              $isArchived = !is_null($tour->deleted_at ?? null);
              $hasBookings = (int) ($tour->bookings_count ?? 0);
            @endphp

            <div class="d-flex flex-wrap">
              {{-- Carrito --}}
              <button type="button"
                class="btn btn-primary btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#modalCart{{ $tour->tour_id }}"
                title="Añadir al carrito">
                <i class="fas fa-cart-plus"></i>
              </button>

              {{-- Editar --}}
              <a href="{{ route('admin.tours.edit', $tour) }}"
                class="btn btn-warning btn-sm"
                title="Editar">
                <i class="fas fa-edit"></i>
              </a>

              {{-- Toggle activo/inactivo --}}
              @unless($isArchived)
                <form action="{{ route('admin.tours.toggle', $tour) }}"
                  method="POST"
                  class="d-inline js-toggle-form"
                  data-question="{{ $tour->is_active ? '¿Desactivar tour?' : '¿Activar tour?' }}">
                  @csrf
                  @method('PATCH')
                  <button type="submit"
                    class="btn btn-sm btn-{{ $tour->is_active ? 'success' : 'secondary' }}"
                    title="{{ $tour->is_active ? 'Desactivar' : 'Activar' }}">
                    <i class="fas fa-toggle-{{ $tour->is_active ? 'on' : 'off' }}"></i>
                  </button>
                </form>
              @endunless

              {{-- Gestionar Precios --}}
              <a href="{{ route('admin.tours.prices.index', $tour) }}"
                class="btn btn-info btn-sm"
                title="Gestionar precios">
                <i class="fas fa-dollar-sign"></i>
              </a>

              {{-- Gestionar Imágenes --}}
              <a href="{{ route('admin.tours.images.index', $tour) }}"
                class="btn btn-secondary btn-sm"
                title="Gestionar imágenes">
                <i class="fas fa-images"></i>
              </a>

              {{-- Eliminar --}}
              @unless($isArchived)
                <form id="delete-form-{{ $tour->tour_id }}"
                  action="{{ route('admin.tours.destroy', $tour) }}"
                  method="POST"
                  class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button type="button"
                    class="btn btn-danger btn-sm"
                    title="Eliminar"
                    onclick="confirmDelete({{ $tour->tour_id }})">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </form>
              @endunless

              {{-- Restaurar --}}
              @if($isArchived)
                <form action="{{ route('admin.tours.restore', $tour) }}"
                  method="POST"
                  class="d-inline">
                  @csrf
                  <button type="submit"
                    class="btn btn-success btn-sm"
                    title="Restaurar">
                    <i class="fas fa-undo"></i>
                  </button>
                </form>
              @endif

              {{-- Eliminar definitivamente --}}
              @if($isArchived)
                <form id="purge-form-{{ $tour->tour_id }}"
                  action="{{ route('admin.tours.purge', $tour) }}"
                  method="POST"
                  class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button type="button"
                    class="btn btn-outline-danger btn-sm"
                    title="Eliminar definitivamente"
                    onclick="confirmPurge({{ $tour->tour_id }}, {{ $hasBookings }})">
                    <i class="fas fa-times"></i>
                  </button>
                </form>
              @endif
            </div>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

{{-- Paginación --}}
@if($tours instanceof \Illuminate\Contracts\Pagination\Paginator)
  <div class="mt-3" id="paginationLinks">
    {{ $tours->withQueryString()->links() }}
  </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Confirmar eliminación
function confirmDelete(id) {
  Swal.fire({
    title: '¿Eliminar tour?',
    text: 'El tour se moverá a Eliminados. Podrás restaurarlo después.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d'
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById('delete-form-' + id).submit();
    }
  });
}

// Confirmar purga
function confirmPurge(id, hasBookings) {
  const extra = hasBookings > 0
    ? `<div class="mt-2 text-start">
         <strong>Advertencia:</strong> Este tour tiene ${hasBookings} reserva(s).
         <br>Las reservas NO se eliminarán, solo quedarán sin tour asociado.
       </div>`
    : '';

  Swal.fire({
    title: '¿Eliminar definitivamente?',
    html: 'Esta acción es irreversible.' + extra,
    icon: 'error',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d'
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById('purge-form-' + id).submit();
    }
  });
}

document.addEventListener('DOMContentLoaded', () => {
  // Toggle forms
  document.addEventListener('submit', function(ev) {
    const form = ev.target;
    if (!form.matches('.js-toggle-form')) return;

    ev.preventDefault();
    const question = form.dataset.question || '¿Confirmar acción?';

    Swal.fire({
      icon: 'question',
      title: 'Confirmar',
      text: question,
      showCancelButton: true,
      confirmButtonText: 'Sí',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#0d6efd',
      cancelButtonColor: '#6c757d'
    }).then(res => {
      if (res.isConfirmed) form.submit();
    });
  });

  // Control de tamaño de fuente - CAMBIADO A 100% POR DEFECTO
  const root = document.documentElement;
  const indicator = document.getElementById('fontIndicator');
  const LS_KEY = 'toursTableFontPct';

  function setPct(pct) {
    pct = Math.max(70, Math.min(150, pct));
    const rem = (pct / 100).toFixed(3) + 'rem';
    root.style.setProperty('--tbl-font-size', rem);

    let mult = 2.2;
    if (pct <= 95) mult = 2.1;
    if (pct <= 90) mult = 2.0;
    if (pct <= 85) mult = 1.9;
    if (pct <= 80) mult = 1.8;
    root.style.setProperty('--btn-cell-mult', String(mult));

    if (indicator) indicator.textContent = pct + '%';
    localStorage.setItem(LS_KEY, String(pct));
  }

  // CAMBIADO: Por defecto 100 en lugar de 90
  const saved = parseInt(localStorage.getItem(LS_KEY) || '100', 10);
  setPct(saved);

  document.getElementById('fontSmaller')?.addEventListener('click', () => {
    const current = parseInt(localStorage.getItem(LS_KEY) || '100', 10);
    setPct(current - 5);
  });

  document.getElementById('fontBigger')?.addEventListener('click', () => {
    const current = parseInt(localStorage.getItem(LS_KEY) || '100', 10);
    setPct(current + 5);
  });

  // Mensajes de sesión
  @if(session('success'))
    Swal.fire({
      icon: 'success',
      title: @json(session('success')),
      timer: 2000,
      showConfirmButton: false
    });
  @endif

  @if(session('error'))
    Swal.fire({
      icon: 'error',
      title: @json(session('error')),
      timer: 2500,
      showConfirmButton: false
    });
  @endif
});
</script>
