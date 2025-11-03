<div class="row">
    <div class="col-md-6">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-check"></i> Incluido
                </h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Selecciona lo que <strong>está incluido</strong> en el tour</label>

                    @php
                        $includedAmenities = $tour ? $tour->amenities->pluck('amenity_id')->toArray() : [];
                    @endphp

                    @forelse($amenities ?? [] as $amenity)
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="included_{{ $amenity->amenity_id }}"
                                   name="included_amenities[]"
                                   value="{{ $amenity->amenity_id }}"
                                   {{ in_array($amenity->amenity_id, old('included_amenities', $includedAmenities)) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="included_{{ $amenity->amenity_id }}">
                                @if($amenity->icon)
                                    <i class="{{ $amenity->icon }}"></i>
                                @endif
                                {{ $amenity->name }}
                            </label>
                        </div>
                    @empty
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            No hay amenidades disponibles.
                        </div>
                    @endforelse
                </div>

                @error('included_amenities')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-times"></i> No Incluido
                </h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Selecciona lo que <strong>NO está incluido</strong> en el tour</label>

                    @php
                        $excludedAmenities = $tour ? $tour->excludedAmenities->pluck('amenity_id')->toArray() : [];
                    @endphp

                    @forelse($amenities ?? [] as $amenity)
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="excluded_{{ $amenity->amenity_id }}"
                                   name="excluded_amenities[]"
                                   value="{{ $amenity->amenity_id }}"
                                   {{ in_array($amenity->amenity_id, old('excluded_amenities', $excludedAmenities)) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="excluded_{{ $amenity->amenity_id }}">
                                @if($amenity->icon)
                                    <i class="{{ $amenity->icon }}"></i>
                                @endif
                                {{ $amenity->name }}
                            </label>
                        </div>
                    @empty
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            No hay amenidades disponibles.
                        </div>
                    @endforelse
                </div>

                @error('excluded_amenities')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i> Ayuda
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Incluido</h5>
                        <p class="small">
                            Marca todo lo que está incluido en el precio del tour
                            (transporte, comidas, entradas, equipo, guía, etc.)
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h5>No Incluido</h5>
                        <p class="small">
                            Marca lo que el cliente debe pagar por separado o traer
                            (propinas, bebidas alcohólicas, souvenirs, etc.)
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($tour ?? false)
    <div class="row">
        <div class="col-md-6">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Incluidos Actuales</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($tour->amenities as $amenity)
                            <li class="list-group-item">
                                @if($amenity->icon)
                                    <i class="{{ $amenity->icon }}"></i>
                                @endif
                                {{ $amenity->name }}
                            </li>
                        @empty
                            <li class="list-group-item text-muted">Ninguno</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Excluidos Actuales</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($tour->excludedAmenities as $amenity)
                            <li class="list-group-item">
                                @if($amenity->icon)
                                    <i class="{{ $amenity->icon }}"></i>
                                @endif
                                {{ $amenity->name }}
                            </li>
                        @empty
                            <li class="list-group-item text-muted">Ninguno</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif
