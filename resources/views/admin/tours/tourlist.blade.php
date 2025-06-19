<style>
/* Estilos responsive y comprimidos */
td.overview-cell,
td.amenities-cell,
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
            <th style="width: 180px;">Amenidades</th>
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
                    @php
                        $horarios = optional($tour->schedules)->sortBy('start_time')->values() ?? collect();
                        $h1 = $horarios->get(0);
                        $h2 = $horarios->get(1);
                        $bloques = [];

                        function esAM($hora) {
                            return $hora && intval(date('H', strtotime($hora))) < 12;
                        }

                        function horaBonita($hora) {
                            return $hora ? date('g:i a', strtotime($hora)) : null;
                        }

                        function bloqueHorario($etiqueta, $inicio, $fin) {
                            return '
                                <div class="border rounded p-1 mb-1 text-center bg-light" style="font-size: 0.7rem;">
                                    <div class="fw-bold text-uppercase">' . $etiqueta . '</div>
                                    <div class="text-dark">' . horaBonita($inicio) . ' - ' . horaBonita($fin) . '</div>
                                </div>
                            ';
                        }

                        if ($h1 && $h1->start_time && $h1->end_time) {
                            $bloques[] = bloqueHorario(esAM($h1->start_time) ? 'AM' : 'PM', $h1->start_time, $h1->end_time);
                        }

                        if ($h2 && $h2->start_time && $h2->end_time) {
                            $bloques[] = bloqueHorario(esAM($h2->start_time) ? 'AM' : 'PM', $h2->start_time, $h2->end_time);
                        }
                    @endphp

                    @if(count($bloques))
                        {!! implode('', $bloques) !!}
                    @else
                        <span class="text-muted">No configurado</span>
                    @endif
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
                    <a href="#" class="btn btn-warning btn-sm"
                       data-bs-toggle="modal"
                       data-bs-target="#modalEditar{{ $tour->tour_id }}">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.tours.destroy', $tour->tour_id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
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

<script>
    function toggleOverview(id, btn) {
        const div = document.getElementById(id);
        div.classList.toggle('overview-expanded');
        btn.textContent = div.classList.contains('overview-expanded') ? 'Ocultar' : 'Ver más';
    }
</script>
