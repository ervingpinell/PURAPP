{{-- Nombre --}}
<div class="form-group">
    <label for="name">
        {{ __('customer_categories.form.name.label') }} <span class="text-danger">*</span>
    </label>
    <input type="text"
           name="name"
           id="name"
           class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $category->name ?? '') }}"
           placeholder="{{ __('customer_categories.form.name.placeholder') }}"
           required>
    @error('name')
        <span class="invalid-feedback">{{ $message ?: __('customer_categories.form.name.required') }}</span>
    @enderror
</div>

{{-- Slug --}}
<div class="form-group">
    <label for="slug">
        {{ __('customer_categories.form.slug.label') }} <span class="text-danger">*</span>
    </label>
    <input type="text"
           name="slug"
           id="slug"
           class="form-control @error('slug') is-invalid @enderror"
           value="{{ old('slug', $category->slug ?? '') }}"
           placeholder="{{ __('customer_categories.form.slug.placeholder') }}"
           required
           pattern="[a-z0-9_-]+"
           title="{{ __('customer_categories.form.slug.title') }}">
    <small class="form-text text-muted">
        {{ __('customer_categories.form.slug.helper') }}
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
                {{ __('customer_categories.form.age_from.label') }} <span class="text-danger">*</span>
            </label>
            <input type="number"
                   name="age_from"
                   id="age_from"
                   class="form-control @error('age_from') is-invalid @enderror"
                   value="{{ old('age_from', $category->age_from ?? '') }}"
                   min="0"
                   max="255"
                   required
                   placeholder="{{ __('customer_categories.form.age_from.placeholder') }}">
            @error('age_from')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- Edad Hasta --}}
    <div class="col-md-6">
        <div class="form-group">
            <label for="age_to">
                {{ __('customer_categories.form.age_to.label') }}
                <small class="text-muted">({{ __('customer_categories.form.age_to.hint_no_limit') }})</small>
            </label>
            <input type="number"
                   name="age_to"
                   id="age_to"
                   class="form-control @error('age_to') is-invalid @enderror"
                   value="{{ old('age_to', $category->age_to ?? '') }}"
                   min="0"
                   max="255"
                   placeholder="{{ __('customer_categories.form.age_to.placeholder') }}">
            @error('age_to')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

{{-- Orden --}}
<div class="form-group">
    <label for="order">
        {{ __('customer_categories.form.order.label') }} <span class="text-danger">*</span>
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
        {{ __('customer_categories.form.order.helper') }}
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
            {{ __('customer_categories.form.active.label') }}
        </label>
    </div>
    <small class="form-text text-muted">
        {{ __('customer_categories.form.active.helper') }}
    </small>
</div>

@push('js')
<script>
    // Auto-generar slug desde el nombre
    document.getElementById('name')?.addEventListener('input', function(e) {
        const slug = e.target.value
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '') // Quitar acentos
            .replace(/[^a-z0-9]+/g, '-')      // Reemplazar no alfanuméricos con -
            .replace(/^-+|-+$/g, '');         // Quitar guiones al inicio/fin

        const slugInput = document.getElementById('slug');
        if (slugInput && !slugInput.dataset.touched) {
            slugInput.value = slug;
        }
    });

    // Marca manual de edición de slug para no sobreescribirlo si el usuario lo cambia
    document.getElementById('slug')?.addEventListener('input', function() {
        this.dataset.touched = '1';
    });

    // Validar que age_to >= age_from
    const ageFrom = document.getElementById('age_from');
    const ageTo = document.getElementById('age_to');
    const msg = @json(__('customer_categories.validation.age_to_gte_age_from'));

    function validateAges() {
        if (ageFrom && ageTo && ageTo.value && ageFrom.value) {
            if (parseInt(ageTo.value, 10) < parseInt(ageFrom.value, 10)) {
                ageTo.setCustomValidity(msg);
                return;
            }
        }
        ageTo.setCustomValidity('');
    }

    ageTo?.addEventListener('input', validateAges);
    ageFrom?.addEventListener('input', validateAges);
</script>
@endpush
