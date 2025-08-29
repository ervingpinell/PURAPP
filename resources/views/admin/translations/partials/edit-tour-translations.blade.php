{{-- Partial: edición de Itinerario e Ítems de un Tour --}}
@if($item->itinerary)
    <!-- Itinerario -->
    <div class="card mb-3">
        <div class="card-header bg-info text-white" data-toggle="collapse" data-target="#collapseItinerary" style="cursor: pointer;">
            <h5 class="mb-0">
                <i class="fas fa-route me-2"></i> {{ __('m_config.translations.itinerary') }}
            </h5>
        </div>
        <div id="collapseItinerary" class="collapse show">
            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="itinerary_name">
                        <i class="far fa-file-alt me-1"></i>
                        {{ __('m_config.translations.itinerary_name') }} ({{ strtoupper($locale) }})
                    </label>
                    <textarea
                        name="itinerary_translations[name]"
                        class="form-control"
                        rows="2"
                    >{{ old('itinerary_translations.name', $item->itinerary->translate($locale)?->name ?? '') }}</textarea>
                </div>
                <div class="form-group mb-3">
                    <label for="itinerary_description">
                        <i class="far fa-file-alt me-1"></i>
                        {{ __('m_config.translations.itinerary_description') }} ({{ strtoupper($locale) }})
                    </label>
                    <textarea
                        name="itinerary_translations[description]"
                        class="form-control"
                        rows="4"
                    >{{ old('itinerary_translations.description', $item->itinerary->translate($locale)?->description ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Ítems del Itinerario -->
    @foreach ($item->itinerary->items as $index => $it)
        <div class="card mb-2">
            <div class="card-header bg-secondary text-white" data-toggle="collapse" data-target="#collapseItem{{ $it->id }}" style="cursor: pointer;">
                <h6 class="mb-0">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    {{ __('m_config.translations.item') }} {{ $index + 1 }}: {{ $it->title }}
                </h6>
            </div>
            <div id="collapseItem{{ $it->id }}" class="collapse">
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="item_title_{{ $it->id }}">
                            <i class="far fa-edit me-1"></i>
                            {{ __('m_config.translations.item_title') }} ({{ strtoupper($locale) }})
                        </label>
                        <textarea
                            name="item_translations[{{ $it->id }}][title]"
                            class="form-control"
                            rows="2"
                        >{{ old("item_translations.$it->id.title", $it->translate($locale)?->title ?? '') }}</textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="item_description_{{ $it->id }}">
                            <i class="far fa-edit me-1"></i>
                            {{ __('m_config.translations.item_description') }} ({{ strtoupper($locale) }})
                        </label>
                        <textarea
                            name="item_translations[{{ $it->id }}][description]"
                            class="form-control"
                            rows="3"
                        >{{ old("item_translations.$it->id.description", $it->translate($locale)?->description ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
