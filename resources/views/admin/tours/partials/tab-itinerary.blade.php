{{-- resources/views/admin/tours/partials/tab-itinerary.blade.php --}}

<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-route"></i> {{ __('m_tours.tour.fields.itinerary') }}
        </h3>
      </div>

      <div class="card-body">
        {{-- Seleccionar Itinerario Existente / Crear nuevo --}}
        <div class="form-group">
          <label for="select-itinerary">{{ __('m_tours.tour.fields.itinerary') }}</label>
          <select name="itinerary_id" id="select-itinerary" class="form-control">
            <option value="">{{ __('m_tours.itinerary.ui.new_itinerary') }}</option>
            @foreach($itineraries ?? [] as $itinerary)
              <option value="{{ $itinerary->itinerary_id }}"
                      {{ old('itinerary_id', $tour->itinerary_id ?? '') == $itinerary->itinerary_id ? 'selected' : '' }}>
                {{ $itinerary->name ?? __('m_tours.itinerary.fields.name') . ' #' . $itinerary->itinerary_id }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Vista previa del itinerario seleccionado --}}
        <div id="view-itinerary-items-create" style="display:none;" class="mt-3">
          <div class="alert alert-info">
            <strong>{{ __('m_tours.itinerary.fields.description') }}:</strong>
            <div id="selected-itinerary-description" class="mt-2"></div>
          </div>
          <h6 class="mb-2">{{ __('m_tours.itinerary_item.ui.list_title') }}</h6>
          <ul class="list-group"></ul>
        </div>

        {{-- Sección para crear nuevo itinerario --}}
        <div id="new-itinerary-section" style="display:none;" class="mt-4">
          <div class="card card-success">
            <div class="card-header">
              <h4 class="card-title mb-0">
                <i class="fas fa-plus"></i> {{ __('m_tours.itinerary.ui.create_title') }}
              </h4>
            </div>
            <div class="card-body">
              {{-- Nombre del nuevo itinerario --}}
              <div class="form-group">
                <label for="new_itinerary_name">
                  {{ __('m_tours.itinerary.fields.name') }} *
                </label>
                <input type="text"
                       name="new_itinerary[name]"
                       id="new_itinerary_name"
                       class="form-control"
                       placeholder="{{ __('m_tours.itinerary.ui.create_title') }}">
                <small class="form-text text-muted">
                  {{ __('m_tours.itinerary.fields.name') }}
                </small>
              </div>

              {{-- Descripción del nuevo itinerario --}}
              <div class="form-group">
                <label for="new_itinerary_description">
                  {{ __('m_tours.itinerary.fields.description') }}
                </label>
                <textarea name="new_itinerary[description]"
                          id="new_itinerary_description"
                          class="form-control"
                          rows="3"
                          placeholder="{{ __('m_tours.itinerary.fields.description_optional') }}"></textarea>
              </div>

              <hr>

              {{-- Seleccionar items existentes para el nuevo itinerario --}}
              <div class="form-group">
                <label>
                  <i class="fas fa-list"></i> {{ __('m_tours.itinerary_item.ui.list_title') }}
                </label>
                <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                  @php
                    $availableItems = \App\Models\ItineraryItem::where('is_active', true)
                                        ->orderBy('title')
                                        ->get();
                  @endphp

                  @forelse($availableItems as $item)
                    <div class="form-check mb-2">
                      <input type="checkbox"
                             class="form-check-input existing-item-checkbox"
                             id="existing_item_{{ $item->item_id }}"
                             value="{{ $item->item_id }}"
                             data-title="{{ $item->title }}"
                             data-description="{{ $item->description }}">
                      <label class="form-check-label" for="existing_item_{{ $item->item_id }}">
                        <strong>{{ $item->title }}</strong>
                        @if($item->description)
                          <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($item->description, 60) }}</small>
                        @endif
                      </label>
                    </div>
                  @empty
                    <p class="text-muted mb-0">
                      {{ __('m_tours.itinerary_item.ui.list_title') }} — {{ __('m_tours.tour.ui.none.itinerary_items') }}
                    </p>
                  @endforelse
                </div>
              </div>

              <hr>

              {{-- Crear nuevos items inline --}}
              <div class="form-group">
                <label>
                  <i class="fas fa-plus-circle"></i> {{ __('m_tours.itinerary_item.ui.register_item') }}
                </label>
                <div id="new-items-container" class="mb-2"></div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-new-item">
                  <i class="fas fa-plus"></i> {{ __('m_tours.itinerary_item.ui.add_item') }}
                </button>
              </div>
            </div>
          </div>
        </div> {{-- /#new-itinerary-section --}}
      </div>
    </div>
  </div>

  {{-- Columna lateral de ayuda / resumen (reusando las mismas llaves del blade previo) --}}
  <div class="col-md-4">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-info-circle"></i> {{ __('m_tours.tour.ui.confirm_title') }}
        </h3>
      </div>
      <div class="card-body">
        <h5>{{ __('m_tours.itinerary.ui.assign') }}</h5>
        <ul>
          <li><strong>{{ __('m_tours.itinerary.ui.assign') }}:</strong> {{ __('m_tours.itinerary.ui.drag_hint') }}</li>
          <li><strong>{{ __('m_tours.itinerary.ui.create_button') }}</strong>: {{ __('m_tours.itinerary.ui.create_title') }}</li>
        </ul>
        <hr>
        <h5>{{ __('m_tours.itinerary.ui.create_title') }}</h5>
        <ul class="small mb-0">
          <li><strong>{{ __('m_tours.itinerary_item.ui.list_title') }}</strong>: {{ __('m_tours.itinerary.ui.drag_hint') }}</li>
          <li><strong>{{ __('m_tours.itinerary_item.ui.register_item') }}</strong>: {{ __('m_tours.itinerary.ui.create_title') }}</li>
          <li><strong>{{ __('m_tours.common.success_title') }}</strong>: {{ __('m_tours.itinerary.ui.save_changes') }}</li>
        </ul>
      </div>
    </div>

    @if($tour ?? false)
      <div class="card card-secondary">
        <div class="card-header">
          <h3 class="card-title">{{ __('m_tours.tour.fields.itinerary') }}</h3>
        </div>
        <div class="card-body">
          @if($tour->itinerary)
            <h6>{{ $tour->itinerary->name ?? __('m_tours.itinerary.fields.name') }}</h6>
            @if($tour->itinerary->description)
              <p class="text-muted small">{{ $tour->itinerary->description }}</p>
            @endif
            @if($tour->itinerary->items->isNotEmpty())
              <hr>
              <ol class="mb-0">
                @foreach($tour->itinerary->items as $item)
                  <li>{{ $item->title }}</li>
                @endforeach
              </ol>
            @endif
          @else
            <p class="text-muted mb-0">{{ __('m_tours.tour.ui.none.itinerary') }}</p>
          @endif
        </div>
      </div>
    @endif
  </div>
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
  let newItemCounter = 0;

  // ========== Agregar nuevo item inline ==========
  document.getElementById('btn-add-new-item')?.addEventListener('click', function() {
    const container = document.getElementById('new-items-container');
    const itemHtml = `
      <div class="card mb-2 new-item-card">
        <div class="card-body p-2">
          <div class="row g-2">
            <div class="col-md-5">
              <input type="text"
                     name="new_itinerary[items][${newItemCounter}][title]"
                     class="form-control form-control-sm"
                     placeholder="{{ __('m_tours.itinerary_item.fields.title') }}"
                     required>
            </div>
            <div class="col-md-6">
              <input type="text"
                     name="new_itinerary[items][${newItemCounter}][description]"
                     class="form-control form-control-sm"
                     placeholder="{{ __('m_tours.itinerary_item.fields.description') }}">
            </div>
            <div class="col-md-1 text-end">
              <button type="button" class="btn btn-sm btn-danger btn-remove-new-item" aria-label="remove item">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHtml);
    newItemCounter++;
  });

  // ========== Remover item nuevo ==========
  document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-remove-new-item')) {
      e.target.closest('.new-item-card')?.remove();
    }
  });

  // ========== Manejar selección de itinerario ==========
  const itinerarySelect = document.getElementById('select-itinerary');
  const newSection = document.getElementById('new-itinerary-section');
  const viewSection = document.getElementById('view-itinerary-items-create');

  if (itinerarySelect) {
    itinerarySelect.addEventListener('change', function() {
      const value = this.value;

      if (!value) {
        // Crear nuevo
        newSection.style.display = 'block';
        viewSection.style.display = 'none';
        return;
      }

      // Mostrar vista previa del itinerario seleccionado
      newSection.style.display = 'none';
      viewSection.style.display = 'block';

      const itineraryData = @json($itineraryJson ?? []);
      const data = itineraryData[value];

      const descEl = document.getElementById('selected-itinerary-description');
      const listEl = viewSection.querySelector('ul');

      if (data) {
        descEl.textContent = data.description || @json(__('m_tours.itinerary.fields.description_optional'));
        listEl.innerHTML = (Array.isArray(data.items) && data.items.length)
          ? data.items.map(item => `
              <li class="list-group-item">
                <strong>${item.title || @json(__('m_tours.itinerary_item.fields.title'))}</strong>
                ${item.description ? `<br><small class="text-muted">${item.description}</small>` : ''}
              </li>
            `).join('')
          : `<li class="list-group-item text-muted">{{ __('m_tours.tour.ui.none.itinerary_items') }}</li>`;
      } else {
        descEl.textContent = @json(__('m_tours.itinerary.fields.description_optional'));
        listEl.innerHTML = `<li class="list-group-item text-muted">{{ __('m_tours.tour.ui.none.itinerary_items') }}</li>`;
      }
    });

    // Trigger inicial
    itinerarySelect.dispatchEvent(new Event('change'));
  }
});
</script>
@endpush
