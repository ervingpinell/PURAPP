{{-- Estilos compactos + controles de fuente --}}
<style>
    /* base vía variable (la setea JS) */
    .table-sm td, .table-sm th { padding: .3rem; font-size: var(--tbl-font-size, 1rem); }

    td.overview-cell, td.amenities-cell, td.not-included-amenities-cell, td.itinerary-cell {
        max-width: 300px; min-width: 150px; white-space: normal; word-break: break-word;
    }
    @media (max-width: 768px){
        td.overview-cell, td.amenities-cell, td.itinerary-cell { max-width: 200px; }
    }
    .overview-preview{
        display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;
        overflow: hidden; max-height: 4.5em; line-height: 1.5em; transition: max-height .3s ease;
        word-break: break-word;
    }
    .overview-expanded{ -webkit-line-clamp: unset; max-height: none; }
    .badge-truncate{ display:inline-block; max-width:100px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; vertical-align:middle; font-size:.75rem; }

    /* Toolbar de tamaño de fuente */
    .font-toolbar{ display:flex; gap:.5rem; align-items:center; margin:.5rem 0 1rem; }
    .font-toolbar .btn{ line-height:1; padding:.25rem .5rem; }
    .font-toolbar .size-indicator{ min-width:3.5rem; text-align:center; font-variant-numeric: tabular-nums; }
</style>

@include('admin.Cart.cartmodal')

{{-- Toolbar de tamaño de fuente --}}
<div class="font-toolbar">
    <button class="btn btn-outline-secondary btn-sm" id="fontSmaller" type="button" title="Reducir tamaño">A−</button>
    <div class="size-indicator" id="fontIndicator">115%</div>
    <button class="btn btn-outline-secondary btn-sm" id="fontBigger" type="button" title="Aumentar tamaño">A+</button>
</div>

<table class="table table-sm table-bordered table-striped table-hover w-100" id="toursTable">
    <thead class="bg-primary text-white">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th style="width: 200px;">Resumen</th>
            <th style="width: 100px;">Amenidades</th>
            <th style="width: 100px;">Exlusiones</th>
            <th style="width: 180px;">Itinerario</th>
            <th class="d-none d-md-table-cell">Idiomas</th>
            <th>Horarios</th>
            <th>Precio Adulto</th>
            <th class="d-none d-md-table-cell">Precio Niño</th>
            <th class="d-none d-md-table-cell">Duración (h)</th>
            <th class="d-none d-md-table-cell">Cupo Máx.</th>
            <th>Tipo</th>
            <th class="d-none d-md-table-cell">Viator Code</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody id="toursTbody">
        @foreach($tours as $tour)
            <tr>
                <td>{{ $tour->tour_id }}</td>
                <td class="text-truncate" style="max-width: 140px;" title="{{ $tour->name }}">{{ $tour->name }}</td>

                {{-- Overview --}}
                <td class="overview-cell">
                    @php $overviewId = 'overview_' . $tour->tour_id; @endphp
                    <div id="{{ $overviewId }}" class="overview-preview">{{ $tour->overview }}</div>
                    <button type="button" class="btn btn-link btn-sm mt-1 p-0"
                        onclick="toggleOverview('{{ $overviewId }}', this)">
                        Ver más
                    </button>
                </td>

                {{-- Amenidades incluidas --}}
                <td class="amenities-cell">
                    @forelse($tour->amenities as $am)
                        <span class="badge bg-info mb-1 badge-truncate" title="{{ $am->name }}">{{ $am->name }}</span>
                    @empty
                        <span class="text-muted">Sin amenidades</span>
                    @endforelse
                </td>

                {{-- Amenidades NO incluidas --}}
                <td class="not-included-amenities-cell">
                    @forelse ($tour->excludedAmenities as $amenity)
                        <span class="badge bg-danger mb-1 badge-truncate" title="{{ $amenity->name }}">{{ $amenity->name }}</span>
                    @empty
                        <span class="text-muted">Sin exclusiones</span>
                    @endforelse
                </td>

                {{-- Itinerario --}}
                <td class="itinerary-cell">
                    @if($tour->itinerary)
                        <strong>{{ $tour->itinerary->name }}</strong><br>
                        @forelse($tour->itinerary->items as $item)
                            <span class="badge bg-info mb-1 badge-truncate" title="{{ $item->title }}">{{ $item->title }}</span>
                        @empty
                            <small class="text-muted">(Sin ítems)</small>
                        @endforelse
                    @else
                        <span class="text-muted">Sin itinerario</span>
                    @endif
                </td>

                {{-- Idiomas --}}
                <td class="d-none d-md-table-cell">
                    @forelse($tour->languages as $lang)
                        <span class="badge bg-secondary">{{ $lang->name }}</span>
                    @empty
                        <span class="text-muted">Sin idiomas</span>
                    @endforelse
                </td>

                {{-- Horarios --}}
                <td>
                    @forelse ($tour->schedules->sortBy('start_time') as $schedule)
                        <div>
                            <span class="badge bg-success">
                                {{ date('g:i A', strtotime($schedule->start_time)) }} -
                                {{ date('g:i A', strtotime($schedule->end_time)) }}
                            </span>
                        </div>
                    @empty
                        <span class="text-muted">Sin horarios</span>
                    @endforelse
                </td>

                <td>{{ number_format($tour->adult_price, 2) }}</td>
                <td class="d-none d-md-table-cell">{{ number_format($tour->kid_price, 2) }}</td>
                <td class="d-none d-md-table-cell">{{ $tour->length }}</td>
                <td class="d-none d-md-table-cell">{{ $tour->max_capacity }}</td>
                <td>{{ $tour->tourType->name }}</td>
                <td class="d-none d-md-table-cell">{{ $tour->viator_code ?? '—' }}</td>

                {{-- Estado --}}
                <td>
                    <span class="badge {{ $tour->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $tour->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>

                {{-- Acciones --}}
                <td>
                    {{-- Carrito --}}
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCart{{ $tour->tour_id }}">
                        <i class="fas fa-cart-plus"></i>
                    </button>

                    <a href="#" class="btn btn-edit btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $tour->tour_id }}">
                        <i class="fas fa-edit"></i>
                    </a>

                    {{-- Toggle con SweetAlert --}}
                    <form action="{{ route('admin.tours.toggle', $tour->tour_id) }}"
                        method="POST"
                        class="d-inline js-toggle-form"
                        data-question="{{ $tour->is_active ? '¿Deseas desactivar este tour?' : '¿Deseas activar este tour?' }}"
                        data-confirm="{{ $tour->is_active ? 'Sí, desactivar' : 'Sí, activar' }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="btn btn-sm btn-toggle"
                                title="{{ $tour->is_active ? 'Desactivar' : 'Activar' }}">
                            <i class="fas fa-toggle-{{ $tour->is_active ? 'on' : 'off' }}"></i>
                        </button>
                    </form>

                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Paginación clásica (usa paginate(8) en el controlador) --}}
@if($tours instanceof \Illuminate\Contracts\Pagination\Paginator)
    <div class="mt-2" id="paginationLinks">
        {{ $tours->withQueryString()->links() }}
    </div>
@endif

{{-- Scroll infinito opcional (mantenlo si quieres auto-carga) --}}
@if($tours instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $tours->hasMorePages())
    <div id="infinite-anchor" class="text-center my-3">
        <button id="btnLoadMore" class="btn btn-outline-primary btn-sm">Cargar más</button>
    </div>
@endif

<script>
    // Ver más / Ocultar overview
    function toggleOverview(id, btn){
        const div = document.getElementById(id);
        div.classList.toggle('overview-expanded');
        btn.textContent = div.classList.contains('overview-expanded') ? 'Ocultar' : 'Ver más';
    }
</script>

{{-- Bootstrap + SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // ========= SweetAlert en Toggle =========
    document.querySelectorAll('.js-toggle-form').forEach(form => {
        form.addEventListener('submit', function(ev){
            ev.preventDefault();
            Swal.fire({
                icon: 'question',
                title: 'Confirmación',
                text: form.dataset.question || '¿Confirmar acción?',
                showCancelButton: true,
                confirmButtonText: form.dataset.confirm || 'Sí, confirmar',
                cancelButtonText: 'Cancelar'
            }).then(res => { if (res.isConfirmed) form.submit(); });
        });
    });

    // ========= Controles de tamaño de fuente =========
    const root = document.documentElement;
    const indicator = document.getElementById('fontIndicator');
    const LS_KEY = 'toursTableFontPct';

    function setPct(pct){
        // límites 100%–150%
        pct = Math.max(100, Math.min(150, pct));
        // La fuente real se define en rem con base 1rem * pct
        const rem = (pct/100).toFixed(3) + 'rem';
        root.style.setProperty('--tbl-font-size', rem);
        indicator.textContent = pct + '%';
        localStorage.setItem(LS_KEY, String(pct));
    }

    // cargar desde LS, por defecto 115%
    const saved = parseInt(localStorage.getItem(LS_KEY) || '115', 10);
    setPct(saved);

    document.getElementById('fontSmaller').addEventListener('click', () => {
        const current = parseInt(localStorage.getItem(LS_KEY) || '115', 10);
        setPct(current - 5);
    });
    document.getElementById('fontBigger').addEventListener('click', () => {
        const current = parseInt(localStorage.getItem(LS_KEY) || '115', 10);
        setPct(current + 5);
    });

    // ========= Scroll infinito (opcional) =========
    const anchor = document.getElementById('infinite-anchor');
    const tbody = document.getElementById('toursTbody');
    const pagLinks = document.getElementById('paginationLinks');

    function nextPageUrl(){
        const nextLink = pagLinks?.querySelector('a[rel="next"]');
        return nextLink ? nextLink.getAttribute('href') : null;
    }

    async function loadMore(url){
        if (!url) return;
        const btn = document.getElementById('btnLoadMore');
        if (btn) { btn.disabled = true; btn.textContent = 'Cargando...'; }

        try{
            const resp = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
            const html = await resp.text();

            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newRows = doc.querySelectorAll('#toursTbody tr');
            newRows.forEach(tr => tbody.appendChild(tr));

            const newPag = doc.querySelector('#paginationLinks');
            if (pagLinks && newPag) pagLinks.innerHTML = newPag.innerHTML;

            const more = nextPageUrl();
            if (!more && anchor) anchor.remove();
            if (btn) { btn.disabled = false; btn.textContent = 'Cargar más'; }

        }catch(e){
            console.error(e);
            Swal.fire({ icon:'error', title:'No se pudo cargar más', timer:1800, showConfirmButton:false });
            if (btn) { btn.disabled = false; btn.textContent = 'Cargar más'; }
        }
    }

    if (anchor){
        document.getElementById('btnLoadMore')?.addEventListener('click', () => loadMore(nextPageUrl()));
        const io = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting){
                const url = nextPageUrl();
                if (url) loadMore(url);
            }
        }, { rootMargin: '400px' });
        io.observe(anchor);
    }

    // ========= SweetAlerts de feedback (sesión) =========
    @if(session('success'))
        Swal.fire({ icon:'success', title:@json(session('success')), timer:2000, showConfirmButton:false });
    @endif
    @if(session('error'))
        Swal.fire({ icon:'error', title:@json(session('error')), timer:2500, showConfirmButton:false });
    @endif
});
</script>
