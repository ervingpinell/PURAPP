@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif
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

