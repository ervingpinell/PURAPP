<table class="table table-bordered table-striped table-hover">
    <thead class="bg-primary text-white">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Resumen</th>
            <th>Descripción</th>
            <th>Amenidades</th>
            <th>Itinerario</th>
            <th>Idiomas</th>
            <th>Horarios</th>
            <th>Precio Adulto</th>
            <th>Precio Niño</th>
            <th>Duración (h)</th>
            <th>Tipo de tour</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tours as $tour)
            <tr>
                <td>{{ $tour->tour_id }}</td>
                <td>{{ $tour->name }}</td>
                <td>{{ $tour->overview }}</td>
                <td>{{ Str::limit($tour->description, 50) }}</td>
                <td>
                    @forelse($tour->amenities as $am)
                        <span class="badge bg-info">{{ $am->name }}</span>
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
                    @forelse($tour->languages as $lang)
                        <span class="badge bg-secondary">{{ $lang->name }}</span>
                    @empty
                        <span class="text-muted">Sin idiomas</span>
                    @endforelse
                </td>
                <td>
                    @php
                        $am = optional($tour->schedules->first());
                        $pm = optional($tour->schedules->skip(1)->first());
                    @endphp
                    @if($am->start_time && $am->end_time)
                        <strong>AM:</strong> {{ $am->start_time }} - {{ $am->end_time }}<br>
                    @endif
                    @if($pm->start_time && $pm->end_time)
                        <strong>PM:</strong> {{ $pm->start_time }} - {{ $pm->end_time }}
                    @endif
                    @if(!$am->start_time && !$pm->start_time)
                        <span class="text-muted">No configurado</span>
                    @endif
                </td>
                <td>{{ number_format($tour->adult_price, 2) }}</td>
                <td>{{ number_format($tour->kid_price, 2) }}</td>
                <td>{{ $tour->length }}</td>
                <td>{{ $tour->tourType->name }}</td>
                <td>
                    @if($tour->is_active)
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-secondary">Inactivo</span>
                    @endif
                </td>
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