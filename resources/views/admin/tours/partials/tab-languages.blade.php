<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Idiomas Disponibles</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Selecciona los idiomas en que se ofrece este tour</label>

                    @php
                        $existingLanguages = $tour ? $tour->languages->pluck('tour_language_id')->toArray() : [];
                    @endphp

                    @forelse($languages ?? [] as $language)
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="language_{{ $language->tour_language_id }}"
                                   name="languages[]"
                                   value="{{ $language->tour_language_id }}"
                                   {{ in_array($language->tour_language_id, old('languages', $existingLanguages)) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="language_{{ $language->tour_language_id }}">
                                <i class="fas fa-language"></i>
                                <strong>{{ $language->name }}</strong>
                                @if($language->code)
                                    <code>{{ strtoupper($language->code) }}</code>
                                @endif
                            </label>
                        </div>
                    @empty
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            No hay idiomas disponibles.
                            <a href="{{ route('admin.languages.index') }}" target="_blank">
                                Gestionar idiomas
                            </a>
                        </div>
                    @endforelse
                </div>

                @error('languages')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i> Información
                </h3>
            </div>
            <div class="card-body">
                <h5>Idiomas del Tour</h5>
                <p class="small">
                    Selecciona todos los idiomas en los que se puede realizar este tour.
                </p>
                <p class="small mb-0">
                    Los clientes podrán elegir el idioma de su preferencia al momento de reservar.
                </p>
            </div>
        </div>

        @if($tour ?? false)
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Idiomas Actuales</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($tour->languages as $language)
                            <li class="list-group-item">
                                <i class="fas fa-language"></i> {{ $language->name }}
                            </li>
                        @empty
                            <li class="list-group-item text-muted">Sin idiomas asignados</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>
