@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif
<table class="table table-bordered table-striped table-hover">
<style>
/* Estilos responsive y comprimidos */
td.overview-cell,
td.amenities-cell,
td.not-included-amenities-cell,
td.itinerary-cell {
    max-width: 300px;
    min-width: 150px;
    white-space: normal;
    word-break: break-word;
}

@media (max-width: 768px) {
    td.overview-cell,
    td.amenities-cell,
    td.itinerary-cell {
        max-width: 200px;
    }
}

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

.badge-truncate {
    display: inline-block;
    max-width: 100px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    vertical-align: middle;
    font-size: 0.75rem;
}

.table-sm td, .table-sm th {
    padding: 0.3rem;
    font-size: 0.85rem;
}
</style>

<table class="table table-sm table-bordered table-striped table-hover w-100">
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
            <th>Tipo</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tours as $tour)
            <tr>
                <td>{{ $tour->tour_id }}</td>
                <td class="text-truncate" style="max-width: 140px;" title="{{ $tour->name }}">{{ $tour->name }}</td>

                {{-- Overview --}}
                <td class="overview-cell">
                    @php $overviewId = 'overview_' . $tour->tour_id; @endphp
                    <div id="{{ $overviewId }}" class="overview-preview">{{ $tour->overview }}</div>
                    <button type="button" class="btn btn-link btn-sm mt-1 p-0" onclick="toggleOverview('{{ $overviewId }}', this)">
                        Ver más
                    </button>
                </td>

                {{-- Amenidades --}}
                <td class="amenities-cell">
                    @forelse($tour->amenities as $am)
                        <span class="badge bg-info mb-1 badge-truncate" title="{{ $am->name }}">
                            {{ $am->name }}
                        </span>
                    @empty
                        <span class="text-muted">Sin amenidades</span>
                    @endforelse
                </td>
                <td>
                    @forelse($tour->itinerary?->items ?? [] as $item)
                        <span class="badge bg-info">{{ $item->title }}</span>
                    @empty
                        <span class="text-muted">Sin itinerarios</span>
                    @endforelse
                </td>
                <td>

                {{-- Amenidades --}}
                <td class="not-included-amenities-cell">
@forelse ($tour->excludedAmenities as $amenity)
    <span class="badge bg-danger mb-1 badge-truncate" title="{{ $amenity->name }}">
        {{ $amenity->name }}
    </span>
@empty
    <span class="text-muted">Sin exclusiones</span>
@endforelse

                </td>

                
                {{-- Itinerario --}}
                <td class="itinerary-cell">
                    @if($tour->itinerary)
                        <strong>{{ $tour->itinerary->name }}</strong><br>
                        @forelse($tour->itinerary->items as $item)
                            <span class="badge bg-info mb-1 badge-truncate" title="{{ $item->title }}">
                                {{ $item->title }}
                            </span>
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
                {{ date('g:i A', strtotime($schedule->start_time)) }} - {{ date('g:i A', strtotime($schedule->end_time)) }}
            </span>
        </div>
    @empty
        <span class="text-muted">Sin horarios</span>
    @endforelse
</td>

                <td>{{ number_format($tour->adult_price, 2) }}</td>
                <td class="d-none d-md-table-cell">{{ number_format($tour->kid_price, 2) }}</td>
                <td class="d-none d-md-table-cell">{{ $tour->length }}</td>
                <td>{{ $tour->tourType->name }}</td>

                {{-- Estado --}}
                <td>
                    <span class="badge {{ $tour->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $tour->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>

                {{-- Acciones --}}
                <td>
                    {{-- Botón carrito --}}
                    <button class="btn btn-success btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalCart{{ $tour->tour_id }}">
                        <i class="fas fa-cart-plus"></i>
                    </button>

                    {{-- Botón editar --}}
                    <a href="#" class="btn btn-warning btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEditar{{ $tour->tour_id }}">
                        <i class="fas fa-edit"></i>
                    </a>

                    {{-- Activar / desactivar --}}
                    <form action="{{ route('admin.tours.destroy', $tour->tour_id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="btn btn-sm {{ $tour->is_active ? 'btn-danger' : 'btn-success' }}"
                            onclick="return confirm('{{ $tour->is_active ? '¿Deseas desactivar este tour?' : '¿Deseas activar este tour?' }}')">
                            <i class="fas {{ $tour->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@foreach($tours as $tour)
<div class="modal fade" id="modalCart{{ $tour->tour_id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.cart.store') }}" class="modal-content">
            @csrf
            <input type="hidden" name="tour_id" value="{{ $tour->tour_id }}">
            <input type="hidden" name="adult_price" value="{{ $tour->adult_price }}">
            <input type="hidden" name="kid_price" value="{{ $tour->kid_price }}">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Agregar al carrito: {{ $tour->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label>Fecha del tour</label>
                    <input type="date" name="tour_date" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Idioma</label>
                    <select name="tour_language_id" class="form-control" required>
                        <option value="">Seleccione</option>
                        @foreach($tour->languages as $lang)
                            <option value="{{ $lang->tour_language_id }}">{{ $lang->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>Cantidad de adultos</label>
                    <input type="number" name="adults_quantity" class="form-control" min="1" value="1" required>
                </div>

                <div class="mb-3">
                    <label>Cantidad de niños</label>
                    <input type="number" name="kids_quantity" class="form-control" min="0" value="0">
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-cart-plus"></i> Agregar al carrito
                </button>
            </div>
        </form>
    </div>
</div>
@endforeach

<script>
    function toggleOverview(id, btn) {
        const div = document.getElementById(id);
        div.classList.toggle('overview-expanded');
        btn.textContent = div.classList.contains('overview-expanded') ? 'Ocultar' : 'Ver más';
    }
</script>
