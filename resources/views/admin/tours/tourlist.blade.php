{{-- resources/views/admin/tours/tourlist.blade.php --}}
<style>
  /* =========================================================
     Variables de escala
     ========================================================= */
  :root {
    --tbl-font-size: 0.9rem;   /* tamaño base de fuente de la tabla (A−/A+ lo cambia) */
    --btn-cell-mult: 2.2;      /* multiplicador base del lado del botón (JS y media queries lo ajustan) */
    --btn-cell-size: calc(var(--tbl-font-size) * var(--btn-cell-mult));
  }

  /* =========================================================
     Tabla responsiva
     ========================================================= */
  .table-responsive-custom {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
  }

  .table-sm td, .table-sm th {
      padding: .3rem;
      font-size: var(--tbl-font-size);
      vertical-align: middle;
  }

  /* Celdas con contenido extenso */
  td.overview-cell,
  td.amenities-cell,
  td.not-included-amenities-cell,
  td.itinerary-cell {
      max-width: 300px;
      min-width: 150px;
      white-space: normal;
      word-break: break-word;
      overflow: hidden;
  }

  /* Contenedor de badges */
  .badges-container {
      display: flex;
      flex-wrap: wrap;
      gap: 0.25rem;
      max-width: 100%;
  }

  td.slug-cell {
      max-width: 180px;
      min-width: 120px;
      font-family: 'Courier New', monospace;
  }

  td.name-cell {
      max-width: 200px;
      min-width: 120px;
      font-weight: 500;
      line-height: 1.3;
      white-space: normal;
      word-break: break-word;
  }

  /* Badges */
  td.amenities-cell .badge,
  td.not-included-amenities-cell .badge,
  td.itinerary-cell .badge {
      padding: 0.2rem 0.35rem;
      margin: 0;
      display: inline-block;
      max-width: 100%;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
  }

  .badge-truncate {
      display: inline-block;
      max-width: 150px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      vertical-align: middle;
  }

  .slug-badge {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 0.25rem 0.5rem;
      border-radius: 0.25rem;
      display: inline-block;
      max-width: 100%;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      font-size: 0.85em;
  }

  /* Overview expandible */
  .overview-preview {
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
      max-height: 4.5em;
      line-height: 1.5em;
      transition: max-height 0.3s ease;
      word-break: break-word;
  }

  .overview-expanded {
      -webkit-line-clamp: unset;
      max-height: none;
  }

  /* Toolbar de fuente */
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

  /* =========================================================
     Botones de acción (escalan con la fuente)
     ========================================================= */
  .actions-cell { min-width: 200px; }

  /* gap uniforme, una sola vez */
  .actions-cell .d-flex { gap: .375rem; }

  /* Tamaño del botón: depende de --tbl-font-size × --btn-cell-mult */
  .actions-cell .btn-sm {
      width:  var(--btn-cell-size);
      height: var(--btn-cell-size);
      padding: 0 !important;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      line-height: 1;
      border-radius: .375rem;
      font-size: var(--tbl-font-size); /* el icono será 1em de esto */
  }

  .actions-cell .btn-sm i,
  .actions-cell .btn-sm svg {
      font-size: 1em;
      width: 1em;
      height: 1em;
  }

  /* =========================================================
     Responsive (ajustan también el multiplicador del botón)
     ========================================================= */
  @media (max-width: 992px) {
      :root { --btn-cell-mult: 2.0; }

      td.overview-cell,
      td.amenities-cell,
      td.not-included-amenities-cell,
      td.itinerary-cell {
          max-width: 250px;
          min-width: 120px;
      }

      td.slug-cell { max-width: 140px; min-width: 100px; }
      td.name-cell { max-width: 150px; min-width: 100px; }
      .badge-truncate { max-width: 110px; }
  }

  @media (max-width: 768px) {
      :root { --btn-cell-mult: 1.8; } /* más compactos en móvil */

      .font-toolbar { justify-content: center; }

      td.overview-cell,
      td.amenities-cell,
      td.not-included-amenities-cell,
      td.itinerary-cell {
          max-width: 200px;
          min-width: 100px;
      }

      td.slug-cell { max-width: 120px; min-width: 90px; }
      td.name-cell { max-width: 120px; min-width: 90px; }
      .badge-truncate { max-width: 90px; }

      .actions-cell { min-width: 180px; }
  }

  /* =========================================================
     Cositas visuales extra
     ========================================================= */
  .table-striped tbody tr:hover {
      background-color: rgba(0, 0, 0, 0.03);
  }

  .schedule-badge {
      display: block;
      margin-bottom: 0.25rem;
  }

  .schedule-badge:last-child {
      margin-bottom: 0;
  }

  /* Scroll horizontal suave */
  .table-responsive-custom::-webkit-scrollbar { height: 8px; }
  .table-responsive-custom::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
  .table-responsive-custom::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
  .table-responsive-custom::-webkit-scrollbar-thumb:hover { background: #555; }

  /* Indicador de scroll */
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
      .scroll-hint { display: block; }
  }
</style>


@include('admin.Cart.cartmodal')

{{-- Toolbar de tamaño de fuente --}}
<div class="font-toolbar">
    <button class="btn btn-outline-secondary btn-sm" id="fontSmaller" type="button"
            title="{{ __('m_tours.tour.ui.font_decrease_title') }}" aria-label="Disminuir tamaño de fuente">
        A−
    </button>
    <div class="size-indicator" id="fontIndicator" aria-live="polite">90%</div>
    <button class="btn btn-outline-secondary btn-sm" id="fontBigger" type="button"
            title="{{ __('m_tours.tour.ui.font_increase_title') }}" aria-label="Aumentar tamaño de fuente">
        A+
    </button>
</div>

{{-- Indicador de scroll horizontal --}}
<div class="scroll-hint">
    <i class="fas fa-arrows-alt-h me-1"></i>
    Desliza horizontalmente para ver más columnas
</div>

<div class="table-responsive-custom">
    <table class="table table-sm table-bordered table-striped table-hover w-100" id="toursTable">
        <thead class="bg-primary text-white">
            <tr>
                <th style="min-width: 60px;">{{ __('m_tours.tour.table.id') }}</th>
                <th style="min-width: 120px;">{{ __('m_tours.tour.table.name') }}</th>
                <th style="min-width: 120px;">{{ __('m_tours.tour.table.slug') ?? 'Slug' }}</th>
                <th style="min-width: 200px;">{{ __('m_tours.tour.table.overview') }}</th>
                <th style="min-width: 150px;">{{ __('m_tours.tour.table.amenities') }}</th>
                <th style="min-width: 150px;">{{ __('m_tours.tour.table.exclusions') }}</th>
                <th style="min-width: 180px;">{{ __('m_tours.tour.table.itinerary') }}</th>
                <th style="min-width: 120px;">{{ __('m_tours.tour.table.schedules') }}</th>
                <th style="min-width: 100px;">{{ __('m_tours.tour.table.adult_price') }}</th>
                <th class="d-none d-md-table-cell" style="min-width: 100px;">{{ __('m_tours.tour.table.kid_price') }}</th>
                <th class="d-none d-md-table-cell" style="min-width: 80px;">{{ __('m_tours.tour.table.length_hours') }}</th>
                <th class="d-none d-md-table-cell" style="min-width: 100px;">{{ __('m_tours.tour.table.max_capacity') }}</th>
                <th style="min-width: 100px;">{{ __('m_tours.tour.table.type') }}</th>
                <th class="d-none d-lg-table-cell" style="min-width: 120px;">{{ __('m_tours.tour.table.viator_code') }}</th>
                <th style="min-width: 90px;">{{ __('m_tours.tour.table.status') }}</th>
                <th style="min-width: 200px;">{{ __('m_tours.tour.table.actions') }}</th>
            </tr>
        </thead>
        <tbody id="toursTbody">
            @foreach($tours as $tour)
                <tr>
                    <td>{{ $tour->tour_id }}</td>
                    <td class="name-cell">{{ $tour->name }}</td>

                    {{-- Slug --}}
                    <td class="slug-cell">
                        @if($tour->slug)
                            <span class="slug-badge" title="{{ $tour->slug }}">{{ $tour->slug }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Overview --}}
                    <td class="overview-cell">
                        @php $overviewId = 'overview_' . $tour->tour_id; @endphp
                        <div id="{{ $overviewId }}" class="overview-preview">{{ $tour->overview }}</div>
                        <button type="button" class="btn btn-link btn-sm mt-1 p-0"
                            onclick="toggleOverview('{{ $overviewId }}', this)">
                            {{ __('m_tours.tour.ui.see_more') }}
                        </button>
                    </td>

                    {{-- Amenidades incluidas --}}
                    <td class="amenities-cell">
                        <div class="badges-container">
                            @forelse($tour->amenities as $am)
                                <span class="badge bg-info badge-truncate" title="{{ $am->name }}">{{ $am->name }}</span>
                            @empty
                                <span class="text-muted">{{ __('m_tours.tour.ui.none.amenities') }}</span>
                            @endforelse
                        </div>
                    </td>

                    {{-- Amenidades NO incluidas --}}
                    <td class="not-included-amenities-cell">
                        <div class="badges-container">
                            @forelse ($tour->excludedAmenities as $amenity)
                                <span class="badge bg-danger badge-truncate" title="{{ $amenity->name }}">{{ $amenity->name }}</span>
                            @empty
                                <span class="text-muted">{{ __('m_tours.tour.ui.none.exclusions') }}</span>
                            @endforelse
                        </div>
                    </td>

                    {{-- Itinerario --}}
                    <td class="itinerary-cell">
                        @if($tour->itinerary)
                            <div class="mb-1">
                                <strong class="d-block text-truncate" title="{{ $tour->itinerary->name }}">{{ $tour->itinerary->name }}</strong>
                            </div>
                            <div class="badges-container">
                                @forelse($tour->itinerary->items as $item)
                                    <span class="badge bg-info badge-truncate" title="{{ $item->title }}">{{ $item->title }}</span>
                                @empty
                                    <span class="text-muted">{{ __('m_tours.tour.ui.none.itinerary_items') }}</span>
                                @endforelse
                            </div>
                        @else
                            <span class="text-muted">{{ __('m_tours.tour.ui.none.itinerary') }}</span>
                        @endif
                    </td>

                    {{-- Horarios --}}
                    <td>
                        @forelse ($tour->schedules->sortBy('start_time') as $schedule)
                            <div class="schedule-badge">
                                <span class="badge bg-success">
                                    {{ date('g:i A', strtotime($schedule->start_time)) }} -
                                    {{ date('g:i A', strtotime($schedule->end_time)) }}
                                </span>
                            </div>
                        @empty
                            <span class="text-muted">{{ __('m_tours.tour.ui.none.schedules') }}</span>
                        @endforelse
                    </td>

                    <td>${{ number_format($tour->adult_price, 2) }}</td>
                    <td class="d-none d-md-table-cell">${{ number_format($tour->kid_price, 2) }}</td>
                    <td class="d-none d-md-table-cell">{{ $tour->length }}h</td>
                    <td class="d-none d-md-table-cell">{{ $tour->max_capacity }}</td>
                    <td>{{ $tour->tourType->name }}</td>
                    <td class="d-none d-lg-table-cell">{{ $tour->viator_code ?? '—' }}</td>

                    {{-- Estado --}}
                    <td>
                        <span class="badge {{ $tour->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $tour->is_active ? __('m_tours.tour.status.active') : __('m_tours.tour.status.inactive') }}
                        </span>
                    </td>

                    {{-- Acciones --}}
{{-- Acciones --}}
<td class="actions-cell">
  @php
      $isArchived  = !is_null($tour->deleted_at ?? null);
      $hasBookings = (int) ($tour->bookings_count ?? 0);
  @endphp

  <div class="d-flex flex-wrap gap-1">
    {{-- Carrito (modal) --}}
    <button type="button"
            class="btn btn-primary btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#modalCart{{ $tour->tour_id }}"
            title="{{ __('m_tours.tour.ui.add_to_cart') ?? 'Añadir al carrito' }}"
            aria-label="{{ __('m_tours.tour.ui.add_to_cart') ?? 'Añadir al carrito' }}">
      <i class="fas fa-cart-plus"></i>
    </button>

    {{-- Editar (modal) --}}
    <button type="button"
            class="btn btn-warning btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#modalEditar{{ $tour->tour_id }}"
            title="{{ __('m_tours.tour.ui.edit') ?? 'Editar' }}"
            aria-label="{{ __('m_tours.tour.ui.edit') ?? 'Editar' }}">
      <i class="fas fa-edit"></i>
    </button>

    {{-- Toggle activo/inactivo (solo si NO está archivado/soft) --}}
    @unless($isArchived)
      <form action="{{ route('admin.tours.toggle', ['tour' => $tour->tour_id]) }}"
            method="POST"
            class="d-inline js-toggle-form"
            data-question="{{ $tour->is_active ? __('m_tours.tour.ui.toggle_off_title') : __('m_tours.tour.ui.toggle_on_title') }}"
            data-confirm="{{ $tour->is_active ? __('m_tours.tour.ui.toggle_off_button') : __('m_tours.tour.ui.toggle_on_button') }}">
        @csrf
        @method('PATCH')
        <button type="submit"
                class="btn btn-sm btn-{{ $tour->is_active ? 'success' : 'secondary' }}"
                title="{{ $tour->is_active ? __('m_tours.tour.ui.toggle_off') : __('m_tours.tour.ui.toggle_on') }}"
                aria-label="{{ $tour->is_active ? __('m_tours.tour.ui.toggle_off') : __('m_tours.tour.ui.toggle_on') }}">
          <i class="fas fa-toggle-{{ $tour->is_active ? 'on' : 'off' }}"></i>
        </button>
      </form>
    @endunless

    {{-- Gestionar imágenes (enlace real, pero visualmente botón cuadrado) --}}
    <a href="{{ route('admin.tours.images.index', ['tour' => $tour->tour_id]) }}"
       class="btn btn-info btn-sm d-inline-flex align-items-center justify-content-center"
       role="button"
       title="{{ __('m_tours.tour.ui.manage_images') ?? 'Gestionar imágenes' }}"
       aria-label="{{ __('m_tours.tour.ui.manage_images') ?? 'Gestionar imágenes' }}">
      <i class="fas fa-images"></i>
    </a>

    {{-- Eliminar (soft delete) --}}
    @unless($isArchived)
      <form id="delete-form-{{ $tour->tour_id }}"
            action="{{ route('admin.tours.destroy', ['tour' => $tour->tour_id]) }}"
            method="POST"
            class="d-inline">
        @csrf
        @method('DELETE')
        <button type="button"
                class="btn btn-danger btn-sm"
                title="{{ __('m_tours.tour.ui.delete_tour') ?? 'Eliminar' }}"
                aria-label="{{ __('m_tours.tour.ui.delete_tour') ?? 'Eliminar' }}"
                onclick="confirmDelete({{ $tour->tour_id }})">
          <i class="fas fa-trash-alt"></i>
        </button>
      </form>
    @endunless

    {{-- Restaurar (si está en Eliminados) --}}
    @if($isArchived)
      <form id="restore-form-{{ $tour->tour_id }}"
            action="{{ route('admin.tours.restore', ['tour' => $tour->tour_id]) }}"
            method="POST"
            class="d-inline">
        @csrf
        <button type="submit"
                class="btn btn-success btn-sm"
                title="{{ __('m_tours.tour.ui.restore') ?? 'Restaurar' }}"
                aria-label="{{ __('m_tours.tour.ui.restore') ?? 'Restaurar' }}">
          <i class="fas fa-undo"></i>
        </button>
      </form>
    @endif

    {{-- Eliminar definitivamente (si está en Eliminados) --}}
    @if($isArchived)
      <form id="purge-form-{{ $tour->tour_id }}"
            action="{{ route('admin.tours.purge', ['tour' => $tour->tour_id]) }}"
            method="POST"
            class="d-inline"
            data-has-bookings="{{ $hasBookings }}">
        @csrf
        @method('DELETE')
        <button type="button"
                class="btn btn-outline-danger btn-sm"
                title="{{ __('m_tours.tour.ui.purge') ?? 'Eliminar definitivamente' }}"
                aria-label="{{ __('m_tours.tour.ui.purge') ?? 'Eliminar definitivamente' }}"
                onclick="confirmPurge({{ $tour->tour_id }}, {{ $hasBookings }})">
          <i class="fas fa-trash"></i>
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

@if($tours instanceof \Illuminate\Contracts\Pagination\Paginator)
    <div class="mt-3" id="paginationLinks">
        {{ $tours->withQueryString()->links() }}
    </div>
@endif

@if($tours instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $tours->hasMorePages())
    <div id="infinite-anchor" class="text-center my-3">
        <button id="btnLoadMore" class="btn btn-outline-primary btn-sm">
            {{ __('m_tours.tour.ui.load_more') }}
        </button>
    </div>
@endif
<script>
  // === Expandir/contraer overview ===
  function toggleOverview(id, btn) {
    const div = document.getElementById(id);
    if (!div) return;

    div.classList.toggle('overview-expanded');
    btn.textContent = div.classList.contains('overview-expanded')
      ? '{{ __('m_tours.tour.ui.see_less') }}'
      : '{{ __('m_tours.tour.ui.see_more') }}';
  }

  // === Confirmar eliminación (soft delete) ===
  function confirmDelete(id) {
    Swal.fire({
      title: '{{ __('m_tours.tour.ui.delete_title') ?? '¿Eliminar tour?' }}',
      html: '{{ __('m_tours.tour.ui.confirm_text') ?? 'El tour pasará a la sección "Eliminados". Podrás restaurarlo luego.' }}',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: '{{ __('m_tours.tour.ui.yes_confirm') ?? 'Sí, eliminar' }}',
      cancelButtonText: '{{ __('m_tours.tour.ui.cancel') ?? 'Cancelar' }}',
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d'
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById('delete-form-' + id)?.submit();
      }
    });
  }

  // === Confirmar purga (delete permanente) ===
  function confirmPurge(id, hasBookings = 0) {
    const n = parseInt(hasBookings, 10) || 0;
    const extra = (n > 0)
      ? `<div class="mt-2 text-start">
           Este tour tiene <b>${n}</b> reserva(s) relacionada(s).
           <br>Al continuar:
           <ul class="text-start" style="margin:.5rem 0 0 1rem;">
             <li>El <b>tour</b> se eliminará <u>definitivamente</u>.</li>
             <li>Las <b>reservas NO se eliminarán</b> y quedarán desasociadas del tour.</li>
           </ul>
         </div>`
      : '';

    Swal.fire({
      title: '{{ __('m_tours.tour.ui.confirm_purge_title') ?? 'Eliminar definitivamente' }}',
      html: '{{ __('m_tours.tour.ui.confirm_purge_text') ?? 'Esta acción es irreversible.' }}' + extra,
      icon: 'error',
      showCancelButton: true,
      confirmButtonText: '{{ __('m_tours.tour.ui.yes_delete') ?? 'Sí, eliminar' }}',
      cancelButtonText: '{{ __('m_tours.tour.ui.cancel') ?? 'Cancelar' }}',
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d'
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById('purge-form-' + id)?.submit();
      }
    });
  }
</script>

<!-- CDNs (manténlos antes del script principal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // ===== Delegación para .js-toggle-form (valido también en filas nuevas por "Load more")
  document.addEventListener('submit', function (ev) {
    const form = ev.target;
    if (!form.matches('.js-toggle-form')) return;

    ev.preventDefault();
    const q = form.dataset.question || '{{ __('m_tours.tour.ui.confirm_text') }}';
    const c = form.dataset.confirm  || '{{ __('m_tours.tour.ui.yes_confirm') }}';

    Swal.fire({
      icon: 'question',
      title: '{{ __('m_tours.tour.ui.confirm_title') }}',
      text: q,
      showCancelButton: true,
      confirmButtonText: c,
      cancelButtonText: '{{ __('m_tours.tour.ui.cancel') }}',
      confirmButtonColor: '#0d6efd',
      cancelButtonColor: '#6c757d'
    }).then(res => {
      if (res.isConfirmed) form.submit();
    });
  });

  // ===== Control de tamaño de fuente + botones (vinculados a --tbl-font-size)
  const root = document.documentElement;
  const indicator = document.getElementById('fontIndicator');
  const LS_KEY = 'toursTableFontPct';

  function setPct(pct) {
    pct = Math.max(70, Math.min(150, pct));

    // 1) Escala de fuente de la tabla
    const rem = (pct / 100).toFixed(3) + 'rem';
    root.style.setProperty('--tbl-font-size', rem);

    // 2) Multiplicador dinámico (botones se “encogen” más a pcts bajos)
    let mult = 2.2;          // base
    if (pct <= 95) mult = 2.1;
    if (pct <= 90) mult = 2.0;
    if (pct <= 85) mult = 1.9;
    if (pct <= 80) mult = 1.8;
    root.style.setProperty('--btn-cell-mult', String(mult));

    // 3) Indicador + persistencia
    if (indicator) indicator.textContent = pct + '%';
    localStorage.setItem(LS_KEY, String(pct));
  }

  const saved = parseInt(localStorage.getItem(LS_KEY) || '90', 10);
  setPct(saved);

  document.getElementById('fontSmaller')?.addEventListener('click', () => {
    const current = parseInt(localStorage.getItem(LS_KEY) || '90', 10);
    setPct(current - 5);
  });
  document.getElementById('fontBigger')?.addEventListener('click', () => {
    const current = parseInt(localStorage.getItem(LS_KEY) || '90', 10);
    setPct(current + 5);
  });

  // ===== Infinite scroll y "Cargar más"
  const anchor   = document.getElementById('infinite-anchor');
  const tbody    = document.getElementById('toursTbody');
  const pagLinks = document.getElementById('paginationLinks');

  function nextPageUrl() {
    const nextLink = pagLinks?.querySelector('a[rel="next"]');
    return nextLink ? nextLink.getAttribute('href') : null;
  }

  function setLoadMoreState(disabled, labelHtml) {
    const btn = document.getElementById('btnLoadMore');
    if (!btn) return;
    btn.disabled = !!disabled;
    btn.innerHTML = labelHtml;
  }

  async function loadMore(url) {
    if (!url) return;
    setLoadMoreState(true, '<span class="spinner-border spinner-border-sm me-1"></span>{{ __('m_tours.tour.ui.loading') }}');

    try {
      const resp = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const html = await resp.text();

      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const newRows = doc.querySelectorAll('#toursTbody tr');
      newRows.forEach(tr => tbody.appendChild(tr));

      const newPag = doc.querySelector('#paginationLinks');
      if (pagLinks && newPag) pagLinks.innerHTML = newPag.innerHTML;

      const more = nextPageUrl();
      if (!more && anchor) anchor.remove();

      setLoadMoreState(false, '{{ __('m_tours.tour.ui.load_more') }}');
    } catch (e) {
      console.error(e);
      Swal.fire({
        icon: 'error',
        title: '{{ __('m_tours.tour.ui.load_more_error') }}',
        timer: 1800,
        showConfirmButton: false
      });
      setLoadMoreState(false, '{{ __('m_tours.tour.ui.load_more') }}');
    }
  }

  if (anchor) {
    document.getElementById('btnLoadMore')?.addEventListener('click', () => loadMore(nextPageUrl()));

    const io = new IntersectionObserver((entries) => {
      if (entries[0].isIntersecting) {
        const url = nextPageUrl();
        if (url) loadMore(url);
      }
    }, { rootMargin: '400px' });
    io.observe(anchor);
  }

  // ===== Mensajes de sesión
  @if(session('success'))
    Swal.fire({ icon: 'success', title: @json(session('success')), timer: 2000, showConfirmButton: false });
  @endif
  @if(session('error'))
    Swal.fire({ icon: 'error', title: @json(session('error')), timer: 2500, showConfirmButton: false });
  @endif
});
</script>
