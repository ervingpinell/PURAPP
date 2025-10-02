@php
  /** @var \App\Models\Tour $item */
  $targetLocale = $locale ?? 'en';
@endphp

@if($item->itinerary)
  @php
    $itinerary   = $item->itinerary;
    $itCollapse  = 'collapseItinerary_' . $itinerary->itinerary_id;
  @endphp

  <!-- Itinerario -->
  <div class="card mb-3">
    <div class="card-header bg-info text-white" data-toggle="collapse" data-target="#{{ $itCollapse }}" style="cursor: pointer;">
      <h5 class="mb-0">
        <i class="fas fa-route mr-2"></i> {{ __('m_config.translations.itinerary') }}
      </h5>
    </div>
    <div id="{{ $itCollapse }}" class="collapse show">
      <div class="card-body">
        <div class="form-group mb-3">
          <label for="itinerary_name">
            <i class="far fa-file-alt mr-1"></i>
            {{ __('m_config.translations.itinerary_name') }} ({{ strtoupper($targetLocale) }})
          </label>
          <textarea
            name="itinerary_translations[name]"
            id="itinerary_name"
            class="form-control"
            rows="2"
          >{{ old('itinerary_translations.name', $itinerary->translate($targetLocale)?->name ?? '') }}</textarea>
        </div>

        <div class="form-group mb-3">
          <label for="itinerary_description">
            <i class="far fa-file-alt mr-1"></i>
            {{ __('m_config.translations.itinerary_description') }} ({{ strtoupper($targetLocale) }})
          </label>
          <textarea
            name="itinerary_translations[description]"
            id="itinerary_description"
            class="form-control"
            rows="4"
          >{{ old('itinerary_translations.description', $itinerary->translate($targetLocale)?->description ?? '') }}</textarea>
        </div>
      </div>
    </div>
  </div>

  <!-- Ítems del Itinerario -->
  @foreach ($itinerary->items as $index => $it)
    @php
      $itemCollapse = 'collapseItem_' . $it->item_id;
      $t = $it->translate($targetLocale);
    @endphp
    <div class="card mb-2">
      <div class="card-header bg-secondary text-white" data-toggle="collapse" data-target="#{{ $itemCollapse }}" style="cursor: pointer;">
        <h6 class="mb-0">
          <i class="fas fa-map-marker-alt mr-2"></i>
          {{ __('m_config.translations.item') }} {{ $index + 1 }}: {{ $it->title }}
        </h6>
      </div>
      <div id="{{ $itemCollapse }}" class="collapse">
        <div class="card-body">
          <div class="form-group mb-3">
            <label for="item_title_{{ $it->item_id }}">
              <i class="far fa-edit mr-1"></i>
              {{ __('m_config.translations.item_title') }} ({{ strtoupper($targetLocale) }})
            </label>
            <textarea
              name="item_translations[{{ $it->item_id }}][title]"
              id="item_title_{{ $it->item_id }}"
              class="form-control"
              rows="2"
            >{{ old("item_translations.{$it->item_id}.title", $t->title ?? '') }}</textarea>
          </div>

          <div class="form-group mb-3">
            <label for="item_description_{{ $it->item_id }}">
              <i class="far fa-edit mr-1"></i>
              {{ __('m_config.translations.item_description') }} ({{ strtoupper($targetLocale) }})
            </label>
            <textarea
              name="item_translations[{{ $it->item_id }}][description]"
              id="item_description_{{ $it->item_id }}"
              class="form-control"
              rows="3"
            >{{ old("item_translations.{$it->item_id}.description", $t->description ?? '') }}</textarea>
          </div>
        </div>
      </div>
    </div>
  @endforeach
@endif
