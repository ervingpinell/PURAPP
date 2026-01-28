<!-- Modal editar itinerario con pestañas de traducción -->
<div class="modal fade" id="modalEditar{{ $itinerary->itinerary_id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form action="{{ route('admin.products.itinerary.update', $itinerary->itinerary_id) }}"
      method="POST"
      class="form-edit-itinerary-translations">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ __('m_tours.itinerary.ui.edit') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('m_tours.itinerary.ui.close') }}"></button>
        </div>
        <div class="modal-body">
          <!-- Tabs de idiomas -->
          <ul class="nav nav-tabs mb-3" id="itineraryTabs{{ $itinerary->itinerary_id }}" role="tablist">
            @foreach(config('app.supported_locales', ['es', 'en', 'fr', 'de', 'pt']) as $index => $locale)
            <li class="nav-item" role="presentation">
              <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                id="tab-{{ $locale }}-{{ $itinerary->itinerary_id }}"
                data-toggle="tab"
                data-target="#content-{{ $locale }}-{{ $itinerary->itinerary_id }}"
                type="button"
                role="tab">
                {{ strtoupper($locale) }}
                @if($locale === 'es')
                <span class="text-danger">*</span>
                @endif
              </button>
            </li>
            @endforeach
          </ul>

          <!-- Contenido de las pestañas -->
          <div class="tab-content" id="itineraryTabContent{{ $itinerary->itinerary_id }}">
            @foreach(config('app.supported_locales', ['es', 'en', 'fr', 'de', 'pt']) as $index => $locale)
            @php
            // Spatie Translatable: get specific locale value without fallback
            $tName = $itinerary->getTranslation('name', $locale, false);
            $tDesc = $itinerary->getTranslation('description', $locale, false);
            @endphp
            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
              id="content-{{ $locale }}-{{ $itinerary->itinerary_id }}"
              role="tabpanel">

              <div class="mb-3">
                <label for="it-name-{{ $locale }}-{{ $itinerary->itinerary_id }}" class="form-label">
                  {{ __('m_tours.itinerary.fields.name') }}
                  @if($locale === 'es')
                  <span class="text-danger">*</span>
                  @endif
                </label>
                <input type="text"
                  name="translations[{{ $locale }}][name]"
                  id="it-name-{{ $locale }}-{{ $itinerary->itinerary_id }}"
                  class="form-control"
                  value="{{ $tName }}"
                  maxlength="255">
              </div>

              <div class="mb-3">
                <label for="it-desc-{{ $locale }}-{{ $itinerary->itinerary_id }}" class="form-label">
                  {{ __('m_tours.itinerary.fields.description') }}
                </label>
                <textarea name="translations[{{ $locale }}][description]"
                  id="it-desc-{{ $locale }}-{{ $itinerary->itinerary_id }}"
                  class="form-control"
                  rows="4"
                  maxlength="1000">{{ $tDesc }}</textarea>
              </div>
            </div>
            @endforeach
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            {{ __('m_tours.itinerary.ui.cancel') }}
          </button>
          <button type="submit" class="btn btn-warning">
            {{ __('m_tours.itinerary.ui.save') }}
          </button>
        </div>
      </div>
    </form>
  </div>
</div>