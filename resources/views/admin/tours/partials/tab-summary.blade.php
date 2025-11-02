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
                            Sin especificar
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
                        @if($tour && $tour->prices->isNotEmpty())
                            @foreach($tour->prices as $price)
                                <tr>
                                    <td>{{ $price->category->name }}</td>
                                    <td>${{ number_format($price->price, 2) }}</td>
                                    <td>{{ $price->min_quantity }}-{{ $price->max_quantity }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" class="text-muted text-center">
                                    Sin precios configurados
                                </td>
                            </tr>
                        @endif
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
                    @if($tour && $tour->schedules->isNotEmpty())
                        @foreach($tour->schedules as $schedule)
                            <li class="list-group-item">
                                <strong>{{ $schedule->start_time }}</strong> - {{ $schedule->end_time }}
                                @if($schedule->label)
                                    <span class="badge badge-info ml-2">{{ $schedule->label }}</span>
                                @endif
                            </li>
                        @endforeach
                    @else
                        <li class="list-group-item text-muted">
                            Sin horarios asignados
                        </li>
                    @endif
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
                    @if($tour && $tour->languages->isNotEmpty())
                        @foreach($tour->languages as $language)
                            <li class="list-group-item">
                                <i class="fas fa-language"></i> {{ $language->name }}
                            </li>
                        @endforeach
                    @else
                        <li class="list-group-item text-muted">
                            Sin idiomas asignados
                        </li>
                    @endif
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
                @if($tour && $tour->itinerary)
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
                    @if($tour && $tour->amenities->isNotEmpty())
                        @foreach($tour->amenities as $amenity)
                            <li class="list-group-item">
                                @if($amenity->icon)
                                    <i class="{{ $amenity->icon }}"></i>
                                @endif
                                {{ $amenity->name }}
                            </li>
                        @endforeach
                    @else
                        <li class="list-group-item text-muted">
                            Nada incluido especificado
                        </li>
                    @endif
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
                    @if($tour && $tour->excludedAmenities->isNotEmpty())
                        @foreach($tour->excludedAmenities as $amenity)
                            <li class="list-group-item">
                                @if($amenity->icon)
                                    <i class="{{ $amenity->icon }}"></i>
                                @endif
                                {{ $amenity->name }}
                            </li>
                        @endforeach
                    @else
                        <li class="list-group-item text-muted">
                            Nada excluido especificado
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
