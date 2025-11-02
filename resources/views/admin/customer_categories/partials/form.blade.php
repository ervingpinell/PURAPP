{{-- Nombre --}}
<div class="form-group">
    <label for="name">
        Nombre <span class="text-danger">*</span>
    </label>
    <input type="text"
           name="name"
           id="name"
           class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $category->name ?? '') }}"
           placeholder="Ej: Adulto, Niño, Infante"
           required>
    @error('name')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

{{-- Slug --}}
<div class="form-group">
    <label for="slug">
        Slug (identificador único) <span class="text-danger">*</span>
    </label>
    <input type="text"
           name="slug"
           id="slug"
           class="form-control @error('slug') is-invalid @enderror"
           value="{{ old('slug', $category->slug ?? '') }}"
           placeholder="Ej: adult, child, infant"
           required
           pattern="[a-z0-9_-]+"
           title="Solo minúsculas, números, guiones y guiones bajos">
    <small class="form-text text-muted">
        Solo letras minúsculas, números, guiones (-) y guiones bajos (_)
    </small>
    @error('slug')
        <span class="invalid-feedback d-block">{{ $message }}</span>
    @enderror
</div>

<div class="row">
    {{-- Edad Desde --}}
    <div class="col-md-6">
        <div class="form-group">
            <label for="age_from">
                Edad Desde <span class="text-danger">*</span>
            </label>
            <input type="number"
                   name="age_from"
                   id="age_from"
                   class="form-control @error('age_from') is-invalid @enderror"
                   value="{{ old('age_from', $category->age_from ?? '') }}"
                   min="0"
                   max="255"
                   required>
            @error('age_from')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- Edad Hasta --}}
    <div class="col-md-6">
        <div class="form-group">
            <label for="age_to">
                Edad Hasta
                <small class="text-muted">(dejar vacío para sin límite)</small>
            </label>
            <input type="number"
                   name="age_to"
                   id="age_to"
                   class="form-control @error('age_to') is-invalid @enderror"
                   value="{{ old('age_to', $category->age_to ?? '') }}"
                   min="0"
                   max="255"
                   placeholder="Vacío = sin límite (18+)">
            @error('age_to')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

{{-- Orden --}}
<div class="form-group">
    <label for="order">
        Orden de Visualización <span class="text-danger">*</span>
    </label>
    <input type="number"
           name="order"
           id="order"
           class="form-control @error('order') is-invalid @enderror"
           value="{{ old('order', $category->order ?? 1) }}"
           min="0"
           max="255"
           required>
    <small class="form-text text-muted">
        Determina el orden en que aparecen las categorías (menor = primero)
    </small>
    @error('order')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

{{-- Estado --}}
<div class="form-group">
    <div class="custom-control custom-switch">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox"
               class="custom-control-input"
               id="is_active"
               name="is_active"
               value="1"
               {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
        <label class="custom-control-label" for="is_active">
            Categoría activa
        </label>
    </div>
    <small class="form-text text-muted">
        Solo las categorías activas se muestran en los formularios de reserva
    </small>
</div>

@push('js')
<script>
    // Auto-generar slug desde el nombre
    document.getElementById('name').addEventListener('input', function(e) {
        const slug = e.target.value
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '') // Quitar acentos
            .replace(/[^a-z0-9]+/g, '-')      // Reemplazar no alfanuméricos con -
            .replace(/^-+|-+$/g, '');          // Quitar guiones al inicio/fin

        document.getElementById('slug').value = slug;
    });

    // Validar que age_to >= age_from
    const ageFrom = document.getElementById('age_from');
    const ageTo = document.getElementById('age_to');

    ageTo.addEventListener('input', function() {
        if (this.value && parseInt(this.value) < parseInt(ageFrom.value)) {
            this.setCustomValidity('La edad hasta debe ser mayor o igual que la edad desde');
        } else {
            this.setCustomValidity('');
        }
    });

    ageFrom.addEventListener('input', function() {
        if (ageTo.value && parseInt(ageTo.value) < parseInt(this.value)) {
            ageTo.setCustomValidity('La edad hasta debe ser mayor o igual que la edad desde');
        } else {
            ageTo.setCustomValidity('');
        }
    });
</script>
@endpush
