{{-- resources/views/admin/tours/tourlist.blade.php --}}
@push('css')
<style>
  :root { --tbl-font-size: 1rem; --btn-cell-mult: 2.2; --btn-cell-size: calc(var(--tbl-font-size) * var(--btn-cell-mult)); }
  .table-responsive-custom{overflow-x:auto;-webkit-overflow-scrolling:touch}
  .table-sm td,.table-sm th{padding:.5rem;font-size:var(--tbl-font-size);vertical-align:middle}
  .font-toolbar{display:flex;gap:.5rem;align-items:center;margin:.5rem 0 1rem;flex-wrap:wrap}
  .font-toolbar .btn{line-height:1;padding:.25rem .5rem}
  .font-toolbar .size-indicator{min-width:3.5rem;text-align:center;font-variant-numeric:tabular-nums;font-weight:500}
  .actions-cell{min-width:280px}
  .actions-cell .d-flex{gap:.375rem}
  .actions-cell .btn-sm{width:var(--btn-cell-size);height:var(--btn-cell-size);padding:0!important;display:inline-flex;align-items:center;justify-content:center;line-height:1;border-radius:.375rem;font-size:var(--tbl-font-size)}
  .actions-cell .btn-sm i{font-size:1em}
  .schedule-badge{display:inline-block;margin:.15rem;white-space:nowrap}
  .price-item{display:flex;justify-content:space-between;align-items:center;padding:.25rem 0;border-bottom:1px solid #e9ecef}
  .price-item:last-child{border-bottom:none}
  .price-label{font-weight:500;color:#ffffff}
  .price-value{font-weight:600;color:#28a745}
  .price-range{font-size:.75rem;color:#ffffff;margin-left:.5rem}
  .table-striped tbody tr:hover{background-color:rgba(0,0,0,.03)}
  @media (max-width:992px){:root{--btn-cell-mult:2.0}}
  @media (max-width:768px){:root{--btn-cell-mult:1.8}.actions-cell{min-width:240px}}
  .table-responsive-custom::-webkit-scrollbar{height:8px}
  .table-responsive-custom::-webkit-scrollbar-track{background:#f1f1f1;border-radius:4px}
  .table-responsive-custom::-webkit-scrollbar-thumb{background:#888;border-radius:4px}
  .table-responsive-custom::-webkit-scrollbar-thumb:hover{background:#555}
  .scroll-hint{display:none;text-align:center;padding:.5rem;background:#e3f2fd;border-radius:.25rem;margin-bottom:.5rem;font-size:.875rem;color:#1976d2}
  @media (max-width:992px){.scroll-hint{display:block}}
</style>
@endpush


@include('admin.carts.cartmodal')

{{-- Toolbar de tamaño de fuente --}}
@once
<div class="font-toolbar">
  <button class="btn btn-outline-secondary btn-sm" id="fontSmaller" type="button"
          title="{{ __('m_tours.common.font_decrease') }}" aria-label="{{ __('m_tours.common.font_decrease') }}">
    A−
  </button>
  <div class="size-indicator" id="fontIndicator" aria-live="polite">100%</div>
  <button class="btn btn-outline-secondary btn-sm" id="fontBigger" type="button"
          title="{{ __('m_tours.common.font_increase') }}" aria-label="{{ __('m_tours.common.font_increase') }}">
    A+
  </button>
</div>
@endonce


{{-- Indicador de scroll --}}
<div class="scroll-hint">
  <i class="fas fa-arrows-alt-h me-1"></i>
  {{ __('m_tours.tour.ui.scroll_hint') }}
</div>

<div class="table-responsive-custom">
  <table class="table table-sm table-bordered table-striped table-hover" id="toursTable">
    <thead class="bg-primary text-white">
      <tr>
        <th style="min-width:60px;">{{ __('m_tours.tour.table.id') }}</th>
        <th style="min-width:180px;">{{ __('m_tours.tour.table.name') }}</th>
        <th style="min-width:150px;">{{ __('m_tours.tour.table.schedules') }}</th>
        <th style="min-width:180px;">{{ __('m_tours.tour.table.prices') }}</th>
        <th style="min-width:100px;">{{ __('m_tours.tour.table.capacity') }}</th>
        <th style="min-width:100px;">{{ __('m_tours.tour.table.group_size') }}</th>
        <th style="min-width:90px;">{{ __('m_tours.tour.table.status') }}</th>
        <th style="min-width:280px;">{{ __('m_tours.tour.table.actions') }}</th>
      </tr>
    </thead>
    <tbody id="toursTbody">
      @foreach($tours as $tour)
        @php
          $isArchived  = !is_null($tour->deleted_at ?? null);
          $hasBookings = (int) ($tour->bookings_count ?? 0);
        @endphp
        <tr>
          {{-- ID --}}
          <td>{{ $tour->tour_id }}</td>

          {{-- Nombre (con traducción si existe helper) --}}
          <td>
            <strong>
              {{ method_exists($tour, 'getTranslatedName') ? $tour->getTranslatedName(app()->getLocale()) : $tour->name }}
            </strong>
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
              <span class="text-muted">{{ __('m_tours.tour.ui.no_schedules') }}</span>
            @endforelse
          </td>

          {{-- Precios Dinámicos (activos) --}}
          <td>
            @php
              $activePrices = $tour->prices
                ->filter(fn($p) => $p->is_active && $p->category && $p->category->is_active)
                ->sortBy('category.order');
            @endphp

            @if($activePrices->isNotEmpty())
              @foreach($activePrices as $price)
                <div class="price-item">
                  <span class="price-label">
                    {{ $price->category->name }}
                    <span class="price-range">({{ $price->min_quantity }}-{{ $price->max_quantity }})</span>
                  </span>
                @php
                $currency = config('app.currency_symbol', '$');
                @endphp
                <span class="price-value">{{ $currency }}{{ number_format($price->price, 2) }}</span>
                                </div>
              @endforeach
            @else
              <span class="text-muted">{{ __('m_tours.tour.ui.no_prices') }}</span>
            @endif
          </td>

          {{-- Capacidad Máxima --}}
          <td class="text-center">
            <span class="badge bg-info">
              {{ $tour->max_capacity }} {{ __('m_tours.common.people') }}
            </span>
          </td>

          {{-- Group Size --}}
          <td class="text-center">
        <span class="badge text-bg-light">
        {{ $tour->group_size ? $tour->group_size.' '. __('m_tours.common.people') : __('m_tours.common.na') }}
        </span>
          </td>

          {{-- Estado --}}
          <td class="text-center">
            <span class="badge {{ $tour->is_active ? 'bg-success' : 'bg-secondary' }}">
              {{ $tour->is_active ? __('m_tours.common.active') : __('m_tours.common.inactive') }}
            </span>
          </td>

          {{-- Acciones --}}
          <td class="actions-cell">
            <div class="d-flex flex-wrap">
              {{-- Carrito --}}
              <button type="button"
                class="btn btn-primary btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#modalCart{{ $tour->tour_id }}"
                title="{{ __('m_tours.tour.ui.add_to_cart') }}"
                aria-label="{{ __('m_tours.tour.ui.add_to_cart') }}">
                <i class="fas fa-cart-plus"></i>
              </button>

              {{-- Editar --}}
              <a href="{{ route('admin.tours.edit', $tour) }}"
                 class="btn btn-warning btn-sm"
                 title="{{ __('m_tours.tour.ui.edit') }}"
                 aria-label="{{ __('m_tours.tour.ui.edit') }}">
                <i class="fas fa-edit"></i>
              </a>

              {{-- Toggle activo/inactivo --}}
              @unless($isArchived)
                <form action="{{ route('admin.tours.toggle', $tour) }}"
                      method="POST"
                      class="d-inline js-toggle-form"
                      data-question="{{ $tour->is_active ? __('m_tours.tour.alerts.toggle_question_active') : __('m_tours.tour.alerts.toggle_question_inactive') }}">
                  @csrf
                  @method('PATCH')
                  <button type="submit"
                          class="btn btn-sm btn-{{ $tour->is_active ? 'success' : 'secondary' }}"
                          title="{{ $tour->is_active ? __('m_tours.tour.ui.deactivate') : __('m_tours.tour.ui.activate') }}"
                          aria-label="{{ $tour->is_active ? __('m_tours.tour.ui.deactivate') : __('m_tours.tour.ui.activate') }}">
                    <i class="fas fa-toggle-{{ $tour->is_active ? 'on' : 'off' }}"></i>
                  </button>
                </form>
              @endunless

              {{-- Gestionar Precios --}}
              <a href="{{ route('admin.tours.prices.index', $tour) }}"
                 class="btn btn-info btn-sm"
                 title="{{ __('m_tours.tour.ui.manage_prices') }}"
                 aria-label="{{ __('m_tours.tour.ui.manage_prices') }}">
                <i class="fas fa-dollar-sign"></i>
              </a>

              {{-- Gestionar Imágenes --}}
              <a href="{{ route('admin.tours.images.index', $tour) }}"
                 class="btn btn-secondary btn-sm"
                 title="{{ __('m_tours.tour.ui.manage_images') }}"
                 aria-label="{{ __('m_tours.tour.ui.manage_images') }}">
                <i class="fas fa-images"></i>
              </a>

              {{-- Eliminar (soft) --}}
              @unless($isArchived)
                <form id="delete-form-{{ $tour->tour_id }}"
                      action="{{ route('admin.tours.destroy', $tour) }}"
                      method="POST"
                      class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button type="button"
                          class="btn btn-danger btn-sm"
                          title="{{ __('m_tours.tour.ui.delete') }}"
                          aria-label="{{ __('m_tours.tour.ui.delete') }}"
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
                          title="{{ __('m_tours.tour.ui.restore') }}"
                          aria-label="{{ __('m_tours.tour.ui.restore') }}">
                    <i class="fas fa-undo"></i>
                  </button>
                </form>
              @endif

              {{-- Eliminar definitivamente (hard) --}}
              @if($isArchived)
                <form id="purge-form-{{ $tour->tour_id }}"
                      action="{{ route('admin.tours.purge', $tour) }}"
                      method="POST"
                      class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button type="button"
                          class="btn btn-outline-danger btn-sm"
                          title="{{ __('m_tours.tour.ui.purge') }}"
                          aria-label="{{ __('m_tours.tour.ui.purge') }}"
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

@push('js')
<script>
  // Confirmar eliminación (soft)
  function confirmDelete(id) {
    Swal.fire({
      title: @json(__('m_tours.tour.alerts.delete_title')),
      text:  @json(__('m_tours.tour.alerts.delete_text')),
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: @json(__('m_tours.common.confirm_delete')),
      cancelButtonText:  @json(__('m_tours.common.cancel')),
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d'
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById('delete-form-' + id).submit();
      }
    });
  }

  // Confirmar purga (hard)
  function confirmPurge(id, hasBookings) {
    const extra = hasBookings > 0
      ? `<div class="mt-2 text-start">
           <strong>{{ __('m_tours.common.warning') }}:</strong>
           {{ __('m_tours.tour.alerts.purge_text_with_bookings', ['count' => '___COUNT___']) }}
         </div>`.replace('___COUNT___', hasBookings)
      : '';

    Swal.fire({
      title: @json(__('m_tours.tour.alerts.purge_title')),
      html:  @json(__('m_tours.tour.alerts.purge_text')) + extra,
      icon: 'error',
      showCancelButton: true,
      confirmButtonText: @json(__('m_tours.common.confirm_delete')),
      cancelButtonText:  @json(__('m_tours.common.cancel')),
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d'
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById('purge-form-' + id).submit();
      }
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    // Toggle (activar/inactivar)
    document.addEventListener('submit', function(ev) {
      const form = ev.target;
      if (!form.matches('.js-toggle-form')) return;
      ev.preventDefault();
      const question = form.dataset.question || @json(__('m_tours.common.confirm_action'));
      Swal.fire({
        icon: 'question',
        title: @json(__('m_tours.common.confirm')),
        text: question,
        showCancelButton: true,
        confirmButtonText: @json(__('m_tours.common.yes')),
        cancelButtonText:  @json(__('m_tours.common.cancel')),
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d'
      }).then(res => {
        if (res.isConfirmed) form.submit();
      });
    });

    // Control de tamaño de fuente (persistente)
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
  });
</script>
@endpush
