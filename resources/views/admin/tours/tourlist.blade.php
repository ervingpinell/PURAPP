<table class="table table-bordered table-striped table-hover">
    <thead class="bg-primary text-white">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Resumen</th>
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
        @php
            function esAM($hora) {
                return $hora && intval(date('H', strtotime($hora))) < 12;
            }

            function horaBonita($hora) {
                return $hora ? date('g:i a', strtotime($hora)) : null;
            }

            function bloqueHorario($etiqueta, $inicio, $fin) {
                return '
                    <div class="border rounded p-2 mb-2 text-center bg-light">
                        <div class="fw-bold text-uppercase text" style="font-size: 0.8rem;">' . $etiqueta . '</div>
                        <div class="text-dark">' . horaBonita($inicio) . ' - ' . horaBonita($fin) . '</div>
                    </div>
                ';
            }
        @endphp

        @foreach($tours as $tour)
            <tr>
                <td>{{ $tour->tour_id }}</td>
                <td>{{ $tour->name }}</td>
                <td>{{ $tour->overview }}</td>

                <td>
                    @forelse($tour->amenities as $am)
                        <span class="badge bg-info">{{ $am->name }}</span>
                    @empty
                        <span class="text-muted">Sin amenidades</span>
                    @endforelse
                </td>

                <td>
                    @if($tour->itinerary)
                        <strong>{{ $tour->itinerary->name }}</strong><br>
                        @forelse($tour->itinerary->items as $item)
                            <span class="badge bg-info">{{ $item->title }}</span>
                        @empty
                            <small class="text-muted">(Sin ítems)</small>
                        @endforelse
                    @else
                        <span class="text-muted">Sin itinerario</span>
                    @endif
                </td>

                <td>
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
