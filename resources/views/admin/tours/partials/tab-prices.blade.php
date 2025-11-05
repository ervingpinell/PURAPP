{{-- resources/views/admin/tours/partials/tab-prices.blade.php --}}

<div class="alert alert-info">
  <i class="fas fa-info-circle"></i>
  <strong>{{ __('m_tours.tour.pricing.note_title') }}</strong>
  {{ __('m_tours.tour.pricing.note_text') }}
  @if($tour ?? false)
    {{ __('m_tours.tour.pricing.manage_detailed_hint') }}
  @endif
</div>

<div id="prices-container">
  @php
      $categories     = \App\Models\CustomerCategory::active()->ordered()->get();
      $existingPrices = $tour ? $tour->prices->keyBy('category_id') : collect();
  @endphp

  @forelse($categories as $category)
    @php
      $existingPrice = $existingPrices->get($category->category_id);
    @endphp

    <div class="card mb-3">
      <div class="card-header">
        <h4 class="card-title mb-0">
          {{ $category->name }}
          @if(!empty($category->age_range))
            <small class="text-muted">({{ $category->age_range }})</small>
          @endif
        </h4>
      </div>

      <div class="card-body">
        <div class="row">
          {{-- Precio --}}
          <div class="col-md-4">
            <div class="form-group">
              <label for="price_{{ $category->category_id }}">
                {{ __('m_tours.tour.pricing.price_usd') }}
              </label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">$</span>
                </div>
                <input
                  type="number"
                  name="prices[{{ $category->category_id }}][price]"
                  id="price_{{ $category->category_id }}"
                  class="form-control"
                  value="{{ old('prices.'.$category->category_id.'.price', $existingPrice->price ?? '0.00') }}"
                  step="0.01"
                  min="0"
                >
              </div>
            </div>
          </div>

          {{-- Cantidad Mínima --}}
          <div class="col-md-3">
            <div class="form-group">
              <label for="min_{{ $category->category_id }}">
                {{ __('m_tours.tour.pricing.min_quantity') }}
              </label>
              <input
                type="number"
                name="prices[{{ $category->category_id }}][min_quantity]"
                id="min_{{ $category->category_id }}"
                class="form-control"
                value="{{ old('prices.'.$category->category_id.'.min_quantity', $existingPrice->min_quantity ?? 0) }}"
                min="0"
                max="255"
              >
            </div>
          </div>

          {{-- Cantidad Máxima --}}
          <div class="col-md-3">
            <div class="form-group">
              <label for="max_{{ $category->category_id }}">
                {{ __('m_tours.tour.pricing.max_quantity') }}
              </label>
              <input
                type="number"
                name="prices[{{ $category->category_id }}][max_quantity]"
                id="max_{{ $category->category_id }}"
                class="form-control"
                value="{{ old('prices.'.$category->category_id.'.max_quantity', $existingPrice->max_quantity ?? 12) }}"
                min="0"
                max="255"
              >
            </div>
          </div>

          {{-- Activo --}}
          <div class="col-md-2">
            <div class="form-group">
              <label>{{ __('m_tours.tour.pricing.status') }}</label>
              <div class="custom-control custom-switch">
                <input type="hidden" name="prices[{{ $category->category_id }}][is_active]" value="0">
                <input
                  type="checkbox"
                  class="custom-control-input"
                  id="active_{{ $category->category_id }}"
                  name="prices[{{ $category->category_id }}][is_active]"
                  value="1"
                  {{ old('prices.'.$category->category_id.'.is_active', $existingPrice->is_active ?? true) ? 'checked' : '' }}
                >
                <label class="custom-control-label" for="active_{{ $category->category_id }}">
                  {{ __('m_tours.tour.pricing.active') }}
                </label>
              </div>
            </div>
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
    <div class="alert alert-warning">
      <i class="fas fa-exclamation-triangle"></i>
      {{ __('m_tours.tour.pricing.no_categories') }}
      <a href="{{ route('admin.customer_categories.index') }}" target="_blank" class="alert-link">
        {{ __('m_tours.tour.pricing.create_categories_first') }}
      </a>
    </div>
  @endforelse
</div>
