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
  <button
    type="button"
    class="btn btn-sm btn-success"
    data-bs-toggle="modal"
    data-bs-target="#modalCreateCategory"
    aria-label="{{ __('m_tours.tour.pricing.create_category') }}"
  >
    <i class="fas fa-plus" aria-hidden="true"></i>
    {{ __('m_tours.tour.pricing.create_category') }}
  </button>
</div>

@php
  use App\Models\CustomerCategory;

  $categories     = CustomerCategory::active()->ordered()->get();
  $existingPrices = (isset($tour) && $tour)
      ? $tour->prices->keyBy('category_id')
      : collect();
  $currency       = config('app.currency_symbol', '$');
  $locale         = app()->getLocale();

  // Helper para etiqueta de categoría y rango de edad
  $buildLabels = function ($category) use ($locale) {
      // Nombre traducido
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
      if (!$catLabel) {
          $catLabel = $category->name ?? $category->slug ?? '';
      }

      // Rango de edad
      $ageLabel = null;
      if (isset($category->age_range) && $category->age_range !== '') {
          $ageLabel = $category->age_range;
      } else {
          $from = $category->age_from ?? null;
          $to   = $category->age_to ?? null;

          if (!is_null($from) && !is_null($to)) {
              $ageLabel = $from . ' - ' . $to;
          } elseif (!is_null($from) && is_null($to)) {
              $ageLabel = $from . '+';
          } elseif (is_null($from) && !is_null($to)) {
              $ageLabel = '0 - ' . $to;
          }
      }

      return [$catLabel, $ageLabel];
  };
@endphp

{{-- Selector para asignar categorías existentes --}}
<div class="mb-3">
  <label for="category-selector" class="form-label">
    {{ __('m_tours.tour.pricing.add_existing_category') }}
  </label>
  <div class="d-flex flex-wrap gap-2">
    <select id="category-selector" class="form-select form-select-sm w-auto">
      <option value="">
        {{ __('m_tours.tour.pricing.choose_category_placeholder') }}
      </option>
      @foreach($categories as $category)
        @php
          [$catLabel, $ageLabel] = $buildLabels($category);
        @endphp
        <option
          value="{{ $category->category_id }}"
          data-age-range="{{ $ageLabel }}"
          data-slug="{{ $category->slug }}"
        >
          {{ $catLabel }}@if($ageLabel) ({{ $ageLabel }})@endif
        </option>
      @endforeach
    </select>

    <button
      type="button"
      class="btn btn-sm btn-outline-primary"
      id="btn-add-category"
    >
      <i class="fas fa-plus-circle"></i>
      {{ __('m_tours.tour.pricing.add_button') }}
    </button>
  </div>
  <small class="text-muted">
    {{ __('m_tours.tour.pricing.add_existing_hint') }}
  </small>
</div>

{{-- Contenedor de cards de precios (solo las asignadas) --}}
<div id="prices-container">
  @forelse($categories as $category)
    @php
      $existingPrice = $existingPrices->get($category->category_id);
      $oldArray      = old('prices.'.$category->category_id);

      // Si el tour es nuevo y no hay old(), no mostramos esta categoría
      if (!$existingPrice && !$oldArray) {
          continue;
      }

      [$catLabel, $ageLabel] = $buildLabels($category);

      $price = old(
        'prices.'.$category->category_id.'.price',
        number_format((float)($existingPrice->price ?? 0), 2, '.', '')
      );
      $minQ  = old(
        'prices.'.$category->category_id.'.min_quantity',
        (int)($existingPrice->min_quantity ?? 0)
      );
      $maxQ  = old(
        'prices.'.$category->category_id.'.max_quantity',
        (int)($existingPrice->max_quantity ?? 12)
      );
      $isActive = old(
        'prices.'.$category->category_id.'.is_active',
        ($existingPrice->is_active ?? true)
      );
    @endphp

    <div
      class="card mb-3 shadow-sm price-card"
      id="price-card-{{ $category->category_id }}"
      data-category-id="{{ $category->category_id }}"
    >
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <h4 class="card-title mb-0">
            {{ $catLabel }}
            @if($ageLabel)
              <small class="text-muted">({{ $ageLabel }})</small>
            @endif
          </h4>
        </div>

        <div class="d-flex align-items-center gap-2">
          @if(!empty($category->slug))
            <span class="badge bg-secondary">{{ $category->slug }}</span>
          @endif
          <button
            type="button"
            class="btn btn-sm btn-outline-danger"
            onclick="removePriceCard({{ $category->category_id }})"
            aria-label="{{ __('m_tours.tour.pricing.remove_category') }}"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>

      <div class="card-body">
        <div class="row g-3 align-items-end">
          {{-- Precio --}}
          <div class="col-12 col-md-6 col-lg-4">
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
                value="{{ $price }}"
                step="0.01"
                min="0"
                inputmode="decimal"
              >
            </div>
          </div>

          {{-- Cantidad Mínima --}}
          <div class="col-6 col-md-3 col-lg-3">
            <label for="min_{{ $category->category_id }}" class="form-label">
              {{ __('m_tours.tour.pricing.min_quantity') }}
            </label>
            <input
              type="number"
              name="prices[{{ $category->category_id }}][min_quantity]"
              id="min_{{ $category->category_id }}"
              class="form-control"
              value="{{ $minQ }}"
              min="0"
              max="255"
              inputmode="numeric"
            >
          </div>

          {{-- Cantidad Máxima --}}
          <div class="col-6 col-md-3 col-lg-3">
            <label for="max_{{ $category->category_id }}" class="form-label">
              {{ __('m_tours.tour.pricing.max_quantity') }}
            </label>
            <input
              type="number"
              name="prices[{{ $category->category_id }}][max_quantity]"
              id="max_{{ $category->category_id }}"
              class="form-control"
              value="{{ $maxQ }}"
              min="0"
              max="255"
              inputmode="numeric"
            >
          </div>

          {{-- Activo (BS5 switch) --}}
          <div class="col-12 col-md-6 col-lg-2">
            <label class="form-label d-block">
              {{ __('m_tours.tour.pricing.status') }}
            </label>
            <div class="form-check form-switch">
              <input
                type="hidden"
                name="prices[{{ $category->category_id }}][is_active]"
                value="0"
              >
              <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                id="active_{{ $category->category_id }}"
                name="prices[{{ $category->category_id }}][is_active]"
                value="1"
                {{ $isActive ? 'checked' : '' }}
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
      <a
        href="{{ route('admin.customer_categories.index') }}"
        target="_blank"
        class="alert-link"
      >
        {{ __('m_tours.tour.pricing.create_categories_first') }}
      </a>
    </div>
  @endforelse
</div>
