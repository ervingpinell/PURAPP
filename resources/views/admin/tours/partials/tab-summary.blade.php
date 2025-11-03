<div class="callout callout-info">
    <h5><i class="fas fa-eye"></i> Vista Previa del Tour</h5>
    <p>Revisa toda la información antes de {{ $tour ? 'actualizar' : 'crear' }} el tour.</p>
</div>

<div class="row">
    {{-- Detalles Básicos --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i> Detalles Básicos
                </h3>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Nombre:</dt>
                    <dd class="col-sm-8" id="summary-name">
                        {{ $tour->name ?? 'Sin especificar' }}
                    </dd>

                    <dt class="col-sm-4">Slug:</dt>
                    <dd class="col-sm-8" id="summary-slug">
                        <code>{{ $tour->slug ?? 'Se generará automáticamente' }}</code>
                    </dd>

                    <dt class="col-sm-4">Tipo:</dt>
                    <dd class="col-sm-8" id="summary-type">
                        @if($tour && $tour->tourType)
                            {{ $tour->tourType->name }}
                        @else
                            <span id="summary-type-text">Sin especificar</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">Duración:</dt>
                    <dd class="col-sm-8" id="summary-length">
                        {{ $tour->length ?? 'N/A' }} horas
                    </dd>

                    <dt class="col-sm-4">Capacidad:</dt>
                    <dd class="col-sm-8" id="summary-capacity">
                        {{ $tour->max_capacity ?? 'N/A' }} personas
                    </dd>

                    <dt class="col-sm-4">Estado:</dt>
                    <dd class="col-sm-8" id="summary-status">
                        @if($tour && $tour->is_active)
                            <span class="badge badge-success">Activo</span>
                        @else
                            <span class="badge badge-secondary">Inactivo</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">Color:</dt>
                    <dd class="col-sm-8" id="summary-color">
                        <span class="badge" style="background-color: {{ $tour->color ?? '#3490dc' }}; color: white;">
                            {{ $tour->color ?? '#3490dc' }}
                        </span>
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Descripción --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-info">
                <h3 class="card-title">
                    <i class="fas fa-align-left"></i> Descripción
                </h3>
            </div>
            <div class="card-body">
                <p id="summary-overview" class="mb-0">
                    {{ $tour->overview ?? 'Sin descripción' }}
                </p>
            </div>
        </div>
    </div>
</div>

@if($tour ?? false)
{{-- SOLO MOSTRAR ESTAS SECCIONES EN EDIT --}}
<div class="row">
    {{-- Precios --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title">
                    <i class="fas fa-dollar-sign"></i> Precios por Categoría
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Mín-Máx</th>
                        </tr>
                    </thead>
                    <tbody id="summary-prices">
                        @php
                            $activePrices = $tour->prices->filter(function($price) {
                                return $price->is_active &&
                                       $price->category &&
                                       $price->category->is_active;
                            });
                        @endphp
                        @forelse($activePrices as $price)
                            <tr>
                                <td>{{ $price->category->name }}</td>
                                <td>${{ number_format($price->price, 2) }}</td>
                                <td>{{ $price->min_quantity }}-{{ $price->max_quantity }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-muted text-center">
                                    Sin precios activos configurados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Horarios --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning">
                <h3 class="card-title">
                    <i class="fas fa-clock"></i> Horarios
                </h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush" id="summary-schedules">
                    @forelse($tour->schedules as $schedule)
                        <li class="list-group-item">
                            <strong>{{ date('g:i A', strtotime($schedule->start_time)) }} - {{ date('g:i A', strtotime($schedule->end_time)) }}</strong>
                            @if($schedule->label)
                                <span class="badge badge-info ml-2">{{ $schedule->label }}</span>
                            @endif
                        </li>
                    @empty
                        <li class="list-group-item text-muted">
                            Sin horarios asignados
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Idiomas --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-secondary">
                <h3 class="card-title">
                    <i class="fas fa-language"></i> Idiomas
                </h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush" id="summary-languages">
                    @forelse($tour->languages as $language)
                        <li class="list-group-item">
                            <i class="fas fa-language"></i> {{ $language->name }}
                        </li>
                    @empty
                        <li class="list-group-item text-muted">
                            Sin idiomas asignados
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    {{-- Itinerario --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-info">
                <h3 class="card-title">
                    <i class="fas fa-route"></i> Itinerario
                </h3>
            </div>
            <div class="card-body">
                @if($tour->itinerary)
                    <h5>{{ $tour->itinerary->name ?? 'Itinerario' }}</h5>
                    <p class="text-muted small">{{ $tour->itinerary->description }}</p>
                    @if($tour->itinerary->items->isNotEmpty())
                        <ol class="pl-3 mb-0">
                            @foreach($tour->itinerary->items as $item)
                                <li><strong>{{ $item->title }}</strong></li>
                            @endforeach
                        </ol>
                    @endif
                @else
                    <p class="text-muted mb-0">Sin itinerario asignado</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Amenidades Incluidas --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title">
                    <i class="fas fa-check"></i> Incluido
                </h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush" id="summary-included">
                    @forelse($tour->amenities as $amenity)
                        <li class="list-group-item">
                            @if($amenity->icon)
                                <i class="{{ $amenity->icon }}"></i>
                            @endif
                            {{ $amenity->name }}
                        </li>
                    @empty
                        <li class="list-group-item text-muted">
                            Nada incluido especificado
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    {{-- Amenidades Excluidas --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-danger">
                <h3 class="card-title">
                    <i class="fas fa-times"></i> No Incluido
                </h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush" id="summary-excluded">
                    @forelse($tour->excludedAmenities as $amenity)
                        <li class="list-group-item">
                            @if($amenity->icon)
                                <i class="{{ $amenity->icon }}"></i>
                            @endif
                            {{ $amenity->name }}
                        </li>
                    @empty
                        <li class="list-group-item text-muted">
                            Nada excluido especificado
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@else
{{-- EN CREATE: Mensaje informativo --}}
<div class="alert alert-info mt-3">
    <i class="fas fa-info-circle"></i>
    <strong>Nota:</strong> Los horarios, precios, idiomas y amenidades se mostrarán aquí después de guardar el tour.
</div>
@endif

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Actualización dinámica del resumen (solo campos básicos)
    function updateSummary() {
        const nameInput = document.getElementById('name');
        if (nameInput) {
            document.getElementById('summary-name').textContent = nameInput.value || 'Sin especificar';
        }

        const slugInput = document.getElementById('slug');
        if (slugInput) {
            document.getElementById('summary-slug').innerHTML =
                `<code>${slugInput.value || 'Se generará automáticamente'}</code>`;
        }

        const overviewInput = document.getElementById('overview');
        if (overviewInput) {
            document.getElementById('summary-overview').textContent =
                overviewInput.value || 'Sin descripción';
        }

        const lengthInput = document.getElementById('length');
        if (lengthInput) {
            document.getElementById('summary-length').textContent =
                (lengthInput.value || 'N/A') + ' horas';
        }

        const capacityInput = document.getElementById('max_capacity');
        if (capacityInput) {
            document.getElementById('summary-capacity').textContent =
                (capacityInput.value || 'N/A') + ' personas';
        }

        const colorInput = document.getElementById('color');
        if (colorInput) {
            const colorBadge = document.getElementById('summary-color').querySelector('.badge');
            if (colorBadge) {
                colorBadge.style.backgroundColor = colorInput.value;
                colorBadge.textContent = colorInput.value;
            }
        }

        const activeInput = document.getElementById('is_active');
        if (activeInput) {
            const statusBadge = document.getElementById('summary-status');
            if (activeInput.checked) {
                statusBadge.innerHTML = '<span class="badge badge-success">Activo</span>';
            } else {
                statusBadge.innerHTML = '<span class="badge badge-secondary">Inactivo</span>';
            }
        }

        const tourTypeSelect = document.getElementById('tour_type_id');
        if (tourTypeSelect) {
            const typeText = document.getElementById('summary-type-text');
            if (typeText) {
                const selectedOption = tourTypeSelect.options[tourTypeSelect.selectedIndex];
                typeText.textContent = selectedOption.text || 'Sin especificar';
            }
        }
    }

    // Event listeners
    ['name', 'slug', 'overview', 'length', 'max_capacity', 'color', 'is_active', 'tour_type_id'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('input', updateSummary);
            element.addEventListener('change', updateSummary);
        }
    });

    updateSummary();
});
</script>
@endpush
