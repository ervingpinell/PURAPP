{{-- resources/views/admin/tours/tourlist.blade.php --}}
<style>
    .table-sm td, .table-sm th {
        padding: .3rem;
        font-size: var(--tbl-font-size, 0.9rem);
        vertical-align: middle;
    }
    td.overview-cell, td.amenities-cell, td.not-included-amenities-cell, td.itinerary-cell {
        max-width: 300px; min-width: 150px; white-space: normal; word-break: break-word;
    }
    td.slug-cell {
        max-width: 180px;
        font-family: 'Courier New', monospace;
    }
    td.name-cell {
        max-width: 140px;
        font-weight: 500;
        line-height: 1.3;
    }
    td.amenities-cell .badge,
    td.not-included-amenities-cell .badge {
        padding: 0.2rem 0.35rem;
        margin-bottom: 0.15rem;
    }
    @media (max-width: 768px){
        td.overview-cell, td.amenities-cell, td.itinerary-cell { max-width: 200px; }
        td.slug-cell { max-width: 120px; }
    }
    .overview-preview{
        display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;
        overflow: hidden; max-height: 4.5em; line-height: 1.5em; transition: max-height .3s ease;
        word-break: break-word;
    }
    .overview-expanded{ -webkit-line-clamp: unset; max-height: none; }
    .badge-truncate{
        display:inline-block;
        max-width: 85px;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
        vertical-align:middle;
    }
    .font-toolbar{ display:flex; gap:.5rem; align-items:center; margin:.5rem 0 1rem; }
    .font-toolbar .btn{ line-height:1; padding:.25rem .5rem; }
    .font-toolbar .size-indicator{ min-width:3.5rem; text-align:center; font-variant-numeric: tabular-nums; }
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
    }
</style>

@include('admin.Cart.cartmodal')

{{-- Toolbar de tamaño de fuente --}}
<div class="font-toolbar">
    <button class="btn btn-outline-secondary btn-sm" id="fontSmaller" type="button" title="{{ __('m_tours.tour.ui.font_decrease_title') }}">A−</button>
    <div class="size-indicator" id="fontIndicator">90%</div>
    <button class="btn btn-outline-secondary btn-sm" id="fontBigger" type="button" title="{{ __('m_tours.tour.ui.font_increase_title') }}">A+</button>
</div>

<table class="table table-sm table-bordered table-striped table-hover w-100" id="toursTable">
    <thead class="bg-primary text-white">
        <tr>
            <th>{{ __('m_tours.tour.table.id') }}</th>
            <th>{{ __('m_tours.tour.table.name') }}</th>
            <th>{{ __('m_tours.tour.table.slug') ?? 'Slug' }}</th>
            <th style="width: 200px;">{{ __('m_tours.tour.table.overview') }}</th>
            <th style="width: 100px;">{{ __('m_tours.tour.table.amenities') }}</th>
            <th style="width: 100px;">{{ __('m_tours.tour.table.exclusions') }}</th>
            <th style="width: 180px;">{{ __('m_tours.tour.table.itinerary') }}</th>
            <th>{{ __('m_tours.tour.table.schedules') }}</th>
            <th>{{ __('m_tours.tour.table.adult_price') }}</th>
            <th class="d-none d-md-table-cell">{{ __('m_tours.tour.table.kid_price') }}</th>
            <th class="d-none d-md-table-cell">{{ __('m_tours.tour.table.length_hours') }}</th>
            <th class="d-none d-md-table-cell">{{ __('m_tours.tour.table.max_capacity') }}</th>
            <th>{{ __('m_tours.tour.table.type') }}</th>
            <th class="d-none d-md-table-cell">{{ __('m_tours.tour.table.viator_code') }}</th>
            <th>{{ __('m_tours.tour.table.status') }}</th>
            <th>{{ __('m_tours.tour.table.actions') }}</th>
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
                    @forelse($tour->amenities as $am)
                        <span class="badge bg-info badge-truncate" title="{{ $am->name }}">{{ $am->name }}</span>
                    @empty
                        <span class="text-muted">{{ __('m_tours.tour.ui.none.amenities') }}</span>
                    @endforelse
                </td>

                {{-- Amenidades NO incluidas --}}
                <td class="not-included-amenities-cell">
                    @forelse ($tour->excludedAmenities as $amenity)
                        <span class="badge bg-danger badge-truncate" title="{{ $amenity->name }}">{{ $amenity->name }}</span>
                    @empty
                        <span class="text-muted">{{ __('m_tours.tour.ui.none.exclusions') }}</span>
                    @endforelse
                </td>

                {{-- Itinerario --}}
                <td class="itinerary-cell">
                    @if($tour->itinerary)
                        <strong>{{ $tour->itinerary->name }}</strong><br>
                        @forelse($tour->itinerary->items as $item)
                            <span class="badge bg-info mb-1 badge-truncate" title="{{ $item->title }}">{{ $item->title }}</span>
                        @empty
                            <span class="text-muted">{{ __('m_tours.tour.ui.none.itinerary_items') }}</span>
                        @endforelse
                    @else
                        <span class="text-muted">{{ __('m_tours.tour.ui.none.itinerary') }}</span>
                    @endif
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
                        <span class="text-muted">{{ __('m_tours.tour.ui.none.schedules') }}</span>
                    @endforelse
                </td>

                <td>${{ number_format($tour->adult_price, 2) }}</td>
                <td class="d-none d-md-table-cell">${{ number_format($tour->kid_price, 2) }}</td>
                <td class="d-none d-md-table-cell">{{ $tour->length }}h</td>
                <td class="d-none d-md-table-cell">{{ $tour->max_capacity }}</td>
                <td>{{ $tour->tourType->name }}</td>
                <td class="d-none d-md-table-cell">{{ $tour->viator_code ?? '—' }}</td>

                {{-- Estado --}}
                <td>
                    <span class="badge {{ $tour->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $tour->is_active ? __('m_tours.tour.status.active') : __('m_tours.tour.status.inactive') }}
                    </span>
                </td>

                {{-- Acciones --}}
                <td>
                    <div class="d-flex flex-wrap gap-1">
                        {{-- Carrito --}}
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCart{{ $tour->tour_id }}" title="{{ __('m_tours.tour.ui.add_to_cart') ?? 'Añadir al carrito' }}">
                            <i class="fas fa-cart-plus"></i>
                        </button>

                        {{-- Editar --}}
                        <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $tour->tour_id }}" title="{{ __('m_tours.tour.ui.edit') ?? 'Editar' }}">
                            <i class="fas fa-edit"></i>
                        </a>

                        {{-- Toggle activo/inactivo --}}
                        <form action="{{ route('admin.tours.toggle', $tour->tour_id) }}"
                            method="POST"
                            class="d-inline js-toggle-form"
                            data-question="{{ $tour->is_active ? __('m_tours.tour.ui.toggle_off_title') : __('m_tours.tour.ui.toggle_on_title') }}"
                            data-confirm="{{ $tour->is_active ? __('m_tours.tour.ui.toggle_off_button') : __('m_tours.tour.ui.toggle_on_button') }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="btn btn-sm btn-{{ $tour->is_active ? 'success' : 'secondary' }}"
                                    title="{{ $tour->is_active ? __('m_tours.tour.ui.toggle_off') : __('m_tours.tour.ui.toggle_on') }}">
                                <i class="fas fa-toggle-{{ $tour->is_active ? 'on' : 'off' }}"></i>
                            </button>
                        </form>

                        {{-- Gestionar imágenes --}}
                        <a href="{{ route('admin.tours.images.index', $tour->tour_id) }}"
                           class="btn btn-info btn-sm"
                           title="{{ __('m_tours.tour.ui.manage_images') ?? 'Gestionar imágenes' }}">
                            <i class="fas fa-images"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@if($tours instanceof \Illuminate\Contracts\Pagination\Paginator)
    <div class="mt-2" id="paginationLinks">
        {{ $tours->withQueryString()->links() }}
    </div>
@endif

@if($tours instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $tours->hasMorePages())
    <div id="infinite-anchor" class="text-center my-3">
        <button id="btnLoadMore" class="btn btn-outline-primary btn-sm">{{ __('m_tours.tour.ui.load_more') }}</button>
    </div>
@endif

<script>
    function toggleOverview(id, btn){
        const div = document.getElementById(id);
        div.classList.toggle('overview-expanded');
        btn.textContent = div.classList.contains('overview-expanded') ? '{{ __('m_tours.tour.ui.see_less') }}' : '{{ __('m_tours.tour.ui.see_more') }}';
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.js-toggle-form').forEach(form => {
        form.addEventListener('submit', function(ev){
            ev.preventDefault();
            Swal.fire({
                icon: 'question',
                title: '{{ __('m_tours.tour.ui.confirm_title') }}',
                text: form.dataset.question || '{{ __('m_tours.tour.ui.confirm_text') }}',
                showCancelButton: true,
                confirmButtonText: form.dataset.confirm || '{{ __('m_tours.tour.ui.yes_confirm') }}',
                cancelButtonText: '{{ __('m_tours.tour.ui.cancel') }}'
            }).then(res => { if (res.isConfirmed) form.submit(); });
        });
    });

    const root = document.documentElement;
    const indicator = document.getElementById('fontIndicator');
    const LS_KEY = 'toursTableFontPct';

    function setPct(pct){
        pct = Math.max(80, Math.min(150, pct));
        const rem = (pct/100).toFixed(3) + 'rem';
        root.style.setProperty('--tbl-font-size', rem);
        indicator.textContent = pct + '%';
        localStorage.setItem(LS_KEY, String(pct));
    }

    const saved = parseInt(localStorage.getItem(LS_KEY) || '90', 10);
    setPct(saved);

    document.getElementById('fontSmaller').addEventListener('click', () => {
        const current = parseInt(localStorage.getItem(LS_KEY) || '90', 10);
        setPct(current - 5);
    });
    document.getElementById('fontBigger').addEventListener('click', () => {
        const current = parseInt(localStorage.getItem(LS_KEY) || '90', 10);
        setPct(current + 5);
    });

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
        if (btn) { btn.disabled = true; btn.textContent = '{{ __('m_tours.tour.ui.loading') }}'; }

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
            if (btn) { btn.disabled = false; btn.textContent = '{{ __('m_tours.tour.ui.load_more') }}'; }

        }catch(e){
            console.error(e);
            Swal.fire({ icon:'error', title:'{{ __('m_tours.tour.ui.load_more_error') }}', timer:1800, showConfirmButton:false });
            if (btn) { btn.disabled = false; btn.textContent = '{{ __('m_tours.tour.ui.load_more') }}'; }
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

    @if(session('success'))
        Swal.fire({ icon:'success', title:@json(session('success')), timer:2000, showConfirmButton:false });
    @endif
    @if(session('error'))
        Swal.fire({ icon:'error', title:@json(session('error')), timer:2500, showConfirmButton:false });
    @endif
});
</script>
