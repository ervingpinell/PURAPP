@php
  $locales = supported_locales();               // ['es','en','fr','pt','de'] (según tu config)
  $mode    = $mode ?? 'create';                 // create|edit
  $model   = $category ?? null;
@endphp

{{-- Slug --}}
<div class="form-group">
    <label for="slug">
        {{ __('customer_categories.form.slug.label') }} <span class="text-danger">*</span>
    </label>
    <input type="text"
           name="slug"
           id="slug"
           class="form-control @error('slug') is-invalid @enderror"
           value="{{ old('slug', $model->slug ?? '') }}"
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
                   value="{{ old('age_from', $model->age_from ?? '') }}"
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
                   value="{{ old('age_to', $model->age_to ?? '') }}"
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
           value="{{ old('order', $model->order ?? 1) }}"
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
               {{ old('is_active', $model->is_active ?? true) ? 'checked' : '' }}>
        <label class="custom-control-label" for="is_active">
            {{ __('customer_categories.form.active.label') }}
        </label>
    </div>
    <small class="form-text text-muted">
        {{ __('customer_categories.form.active.helper') }}
    </small>
</div>

{{-- Traducciones: Nombres por idioma --}}
<div class="card mt-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="fas fa-language"></i> {{ __('customer_categories.form.translations.title') }}</span>

        @if($mode === 'create')
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="auto_translate" name="auto_translate" value="1" checked>
                <label for="auto_translate" class="custom-control-label">
                    {{ __('customer_categories.form.translations.auto_translate') }}
                </label>
            </div>
        @else
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="regen_missing" name="regen_missing" value="1">
                <label for="regen_missing" class="custom-control-label">
                    {{ __('customer_categories.form.translations.regen_missing') }}
                </label>
            </div>
        @endif
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs" id="langTabs" role="tablist">
            @foreach($locales as $i => $loc)
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $i===0 ? 'active' : '' }}" id="tab-{{ $loc }}" data-toggle="tab" href="#pane-{{ $loc }}" role="tab">
                        {{ strtoupper($loc) }}
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content border-left border-right border-bottom p-3">
            @foreach($locales as $i => $loc)
                @php
                    $existing = $model
                        ? optional($model->translations->firstWhere('locale', $loc))->name
                        : null;
                    $val = old("names.$loc", $existing);
                @endphp

                <div class="tab-pane fade {{ $i===0 ? 'show active' : '' }}" id="pane-{{ $loc }}" role="tabpanel">
                    <div class="form-group mb-0">
                        <label>
                            {{ __('customer_categories.form.name.label') }}
                            @if($i===0) <span class="text-danger">*</span> @endif
                        </label>
                        <input type="text"
                               name="names[{{ $loc }}]"
                               class="form-control @error('names.' . $loc) is-invalid @enderror"
                               value="{{ $val }}"
                               @if($i===0) required @endif
                               placeholder="{{ __('customer_categories.form.name.placeholder') }}">
                        @error('names.' . $loc)
                            <span class="invalid-feedback">{{ $message ?: __('customer_categories.form.name.required') }}</span>
                        @enderror
                        <small class="form-text text-muted">
                            {!! __('customer_categories.form.translations.locale_hint', ['loc' => '<code>'.strtoupper($loc).'</code>']) !!}
                        </small>
                    </div>
                </div>
            @endforeach
        </div>

        <small class="text-muted d-block mt-2">
            {{ __('customer_categories.form.translations.at_least_first') }}
        </small>
    </div>
</div>

@push('js')
<script>
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
