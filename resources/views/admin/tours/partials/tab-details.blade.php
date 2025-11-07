<div class="row">
    <div class="col-md-8">
        {{-- Nombre del Tour --}}
        <div class="form-group">
            <label for="name">
                {{ __('m_tours.tour.fields.name') }} <span class="text-danger">*</span>
            </label>
            <input
                type="text"
                name="name"
                id="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $tour->name ?? '') }}"
                required>
            @error('name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        {{-- Slug --}}
        <div class="form-group">
            <label for="slug">
                {{-- No existe una clave evidente para el rótulo "Slug" en tu archivo.
                     Si quieres agregarla, podrías crear m_tours.tour.fields.slug --}}
                Slug
            </label>
            <input
                type="text"
                name="slug"
                id="slug"
                class="form-control @error('slug') is-invalid @enderror"
                value="{{ old('slug', $tour->slug ?? '') }}"
                placeholder="{{ __('m_tours.tour.ui.generate_auto') }}">
                <div id="slug-feedback" class="mt-1"></div>

            <small class="form-text text-muted">
                {{ __('m_tours.tour.ui.slug_help') }}
            </small>
            @error('slug')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        {{-- Overview --}}
        <div class="form-group">
            <label for="overview">{{ __('m_tours.tour.fields.overview') }}</label>
            <textarea
                name="overview"
                id="overview"
                class="form-control @error('overview') is-invalid @enderror"
                rows="5">{{ old('overview', $tour->overview ?? '') }}</textarea>
            @error('overview')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="row">
            {{-- Duración --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label for="length">{{ __('m_tours.tour.fields.length_hours') }}</label>
                    <input
                        type="number"
                        name="length"
                        id="length"
                        class="form-control @error('length') is-invalid @enderror"
                        value="{{ old('length', $tour->length ?? '') }}"
                        step="0.5"
                        min="0">
                    @error('length')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Capacidad Máxima --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label for="max_capacity">
                        {{ __('m_tours.tour.fields.max_capacity') }} <span class="text-danger">*</span>
                    </label>
                    <input
                        type="number"
                        name="max_capacity"
                        id="max_capacity"
                        class="form-control @error('max_capacity') is-invalid @enderror"
                        value="{{ old('max_capacity', $tour->max_capacity ?? 12) }}"
                        min="1"
                        required>
                    <small class="form-text text-muted">
                        {{ __('m_tours.tour.ui.default_capacity') }}
                    </small>
                    @error('max_capacity')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Tamaño de grupo (capacidad sugerida por tour) --}}
            <div class="form-group col-md-6">
                <label for="group_size">
                    {{ __('m_tours.tour.fields.group_size') }}
                </label>
                <input
                    type="number"
                    name="group_size"
                    id="group_size"
                    class="form-control @error('group_size') is-invalid @enderror"
                    value="{{ old('group_size', optional($tour)->group_size) }}"
                    min="1"
                    step="1"
                    placeholder="{{ __('m_tours.tour.placeholders.group_size') }}">
                @error('group_size')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">
                    {{ __('m_tours.tour.hints.group_size') }}
                </small>
            </div>

        </div>
    </div>

    <div class="col-md-4">
        {{-- Color del Tour --}}
        <div class="form-group">
            <label for="color">{{ __('m_tours.tour.ui.color') }}</label>
            <input
                type="color"
                name="color"
                id="color"
                class="form-control form-control-color @error('color') is-invalid @enderror"
                value="{{ old('color', $tour->color ?? '#3490dc') }}">
            @error('color')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        {{-- Tipo de Tour --}}
        <div class="form-group">
            <label for="tour_type_id">{{ __('m_tours.tour.fields.type') }}</label>
            <select
                name="tour_type_id"
                id="tour_type_id"
                class="form-control @error('tour_type_id') is-invalid @enderror">
                <option value="">{{ '-- ' . __('m_tours.tour.ui.select_type') . ' --' }}</option>
                @foreach($tourTypes ?? [] as $type)
                    <option value="{{ $type->tour_type_id }}"
                            {{ old('tour_type_id', $tour->tour_type_id ?? '') == $type->tour_type_id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
            @error('tour_type_id')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        {{-- Estado Activo --}}
        <div class="form-group">
            <div class="custom-control custom-switch">
                <input type="hidden" name="is_active" value="0">
                <input
                    type="checkbox"
                    class="custom-control-input"
                    id="is_active"
                    name="is_active"
                    value="1"
                    {{ old('is_active', $tour->is_active ?? true) ? 'checked' : '' }}>
                <label class="custom-control-label" for="is_active">
                    {{-- No hay una clave exacta para “Tour Activo”.
                         Puedes usar el campo Status o crear m_tours.tour.ui.active_label --}}
                    {{ __('m_tours.tour.fields.status') }}
                </label>
            </div>
        </div>

        @if($tour ?? false)
            <div class="card card-secondary mt-3">
                <div class="card-header">
                    {{-- Si quieres traducir este título, crea p.ej. m_tours.tour.ui.info_card_title --}}
                    <h3 class="card-title">Información</h3>
                </div>
                <div class="card-body">
                    <p><strong>ID:</strong> {{ $tour->tour_id }}</p>
                    <p><strong>Creado:</strong> {{ $tour->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Actualizado:</strong> {{ $tour->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        @endif
    </div>
</div>

@push('js')
<script>
    // Auto-generar slug desde el nombre (solo si está vacío)
    document.getElementById('name').addEventListener('input', function(e) {
        const slugField = document.getElementById('slug');
        if (!slugField.value || slugField.dataset.autogenerated === 'true') {
            const slug = e.target.value
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugField.value = slug;
            slugField.dataset.autogenerated = 'true';
        }
    });

    // Marcar que el slug fue editado manualmente
    document.getElementById('slug').addEventListener('input', function() {
        if (this.value) {
            this.dataset.autogenerated = 'false';
        }
    });
</script>
@endpush
