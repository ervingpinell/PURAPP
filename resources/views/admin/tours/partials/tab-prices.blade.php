{{-- resources/views/admin/tours/partials/tab-prices.blade.php --}}

<div class="alert alert-info" role="status" aria-live="polite">
  <i class="fas fa-info-circle"></i>
  <strong>{{ __('m_tours.tour.pricing.note_title') }}</strong>
  {{ __('m_tours.tour.pricing.note_text') }}
  @if($tour ?? false)
    {{ __('m_tours.tour.pricing.manage_detailed_hint') }}
  @endif
</div>

{{-- Botón para crear nueva categoría --}}
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0">{{ __('m_tours.tour.pricing.configured_categories') }}</h5>
  <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalCreateCategory" aria-label="{{ __('m_tours.tour.pricing.create_category') }}">
    <i class="fas fa-plus" aria-hidden="true"></i> {{ __('m_tours.tour.pricing.create_category') }}
  </button>
</div>

<div id="prices-container">
@php
  use App\Models\CustomerCategory;

  $categories     = CustomerCategory::active()->ordered()->get();
  $existingPrices = (isset($tour) && $tour) ? $tour->prices->keyBy('category_id') : collect();
  $currency       = config('app.currency_symbol', '$');
  $locale         = app()->getLocale();
@endphp

  @forelse($categories as $category)
    @php
      // === Nombre traducido de la categoría ===
      $catLabel = method_exists($category, 'getTranslatedName')
        ? ($category->getTranslatedName($locale) ?: null)
        : null;

      if (!$catLabel && !empty($category->slug)) {
        foreach ([
          'customer_categories.labels.' . $category->slug,
          'm_tours.customer_categories.labels.' . $category->slug,
        ] as $k) {
          $tr = __($k);
          if ($tr !== $k) { $catLabel = $tr; break; }
        }
      }
      if (!$catLabel) { $catLabel = $category->name ?? $category->slug ?? ''; }

      $existingPrice = $existingPrices->get($category->category_id);
    @endphp

    <div class="card mb-3">
      <div class="card-header">
        <h4 class="card-title mb-0">
          {{ $catLabel }}
          @if(!empty($category->age_range))
            <small class="text-muted">({{ $category->age_range }})</small>
          @endif
        </h4>
      </div>

      <div class="card-body">
        <div class="row g-3">
          {{-- Precio --}}
          <div class="col-md-4">
            <label for="price_{{ $category->category_id }}" class="form-label">
              {{ __('m_tours.tour.pricing.price_usd') }}
            </label>
            <div class="input-group">
              <span class="input-group-text" aria-hidden="true">{{ $currency }}</span>
              <input
                type="number"
                name="prices[{{ $category->category_id }}][price]"
                id="price_{{ $category->category_id }}"
                class="form-control"
                value="{{ old('prices.'.$category->category_id.'.price', number_format((float)($existingPrice->price ?? 0), 2, '.', '')) }}"
                step="0.01"
                min="0"
                inputmode="decimal"
              >
            </div>
          </div>

          {{-- Cantidad Mínima --}}
          <div class="col-md-3">
            <label for="min_{{ $category->category_id }}" class="form-label">
              {{ __('m_tours.tour.pricing.min_quantity') }}
            </label>
            <input
              type="number"
              name="prices[{{ $category->category_id }}][min_quantity]"
              id="min_{{ $category->category_id }}"
              class="form-control"
              value="{{ old('prices.'.$category->category_id.'.min_quantity', (int)($existingPrice->min_quantity ?? 0)) }}"
              min="0"
              max="255"
              inputmode="numeric"
            >
          </div>

          {{-- Cantidad Máxima --}}
          <div class="col-md-3">
            <label for="max_{{ $category->category_id }}" class="form-label">
              {{ __('m_tours.tour.pricing.max_quantity') }}
            </label>
            <input
              type="number"
              name="prices[{{ $category->category_id }}][max_quantity]"
              id="max_{{ $category->category_id }}"
              class="form-control"
              value="{{ old('prices.'.$category->category_id.'.max_quantity', (int)($existingPrice->max_quantity ?? 12)) }}"
              min="0"
              max="255"
              inputmode="numeric"
            >
          </div>

          {{-- Activo (BS5 switch) --}}
          <div class="col-md-2">
            <label class="form-label d-block">{{ __('m_tours.tour.pricing.status') }}</label>
            <div class="form-check form-switch">
              <input type="hidden" name="prices[{{ $category->category_id }}][is_active]" value="0">
              <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                id="active_{{ $category->category_id }}"
                name="prices[{{ $category->category_id }}][is_active]"
                value="1"
                {{ old('prices.'.$category->category_id.'.is_active', ($existingPrice->is_active ?? true)) ? 'checked' : '' }}
              >
              <label class="form-check-label" for="active_{{ $category->category_id }}">
                {{ __('m_tours.tour.pricing.active') }}
              </label>
            </div>
            <small class="text-muted d-block mt-1">
              {{ __('m_tours.tour.pricing.hints.zero_disables') }}
            </small>
          </div>
        </div>
      </div>
    </div>

    {{-- Hidden field para category_id --}}
    <input
      type="hidden"
      name="prices[{{ $category->category_id }}][category_id]"
      value="{{ $category->category_id }}"
    >
  @empty
    <div class="alert alert-warning mb-0" role="alert">
      <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
      {{ __('m_tours.tour.pricing.no_categories') }}
      <a href="{{ route('admin.customer_categories.index') }}" target="_blank" class="alert-link">
        {{ __('m_tours.tour.pricing.create_categories_first') }}
      </a>
    </div>
  @endforelse
</div>
