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
            <option value="">
              {{ __('m_tours.itinerary.ui.new_itinerary') }}
            </option>
            @foreach($itineraries ?? [] as $itinerary)
              <option value="{{ $itinerary->itinerary_id }}"
                      {{ old('itinerary_id', $tour->itinerary_id ?? '') == $itinerary->itinerary_id ? 'selected' : '' }}>
                {{ $itinerary->name ?? __('m_tours.itinerary.fields.name') . ' #' . $itinerary->itinerary_id }}
              </option>
            @endforeach
          </select>
          <small class="form-text text-muted">
            {{ __('m_tours.itinerary.ui.select_or_create_hint') }}
          </small>
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
                       value="{{ old('new_itinerary.name') }}"
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
                          placeholder="{{ __('m_tours.itinerary.fields.description_optional') }}">{{ old('new_itinerary.description') }}</textarea>
              </div>

              <hr>

              {{-- Pool de items existentes + lista ordenable --}}
              <div class="row">
                {{-- Pool de items existentes --}}
                <div class="col-md-6">
                  <div class="form-group">
                    <label>
                      <i class="fas fa-list"></i> {{ __('m_tours.itinerary_item.ui.list_title') }}
                    </label>
                    <p class="text-muted small mb-2">
                      {{ __('m_tours.itinerary_item.ui.pool_hint') }}
                    </p>

                    <div class="border rounded p-3" style="max-height: 260px; overflow-y: auto;">
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
                              <br>
                              <small class="text-muted">
                                {{ \Illuminate\Support\Str::limit($item->description, 80) }}
                              </small>
                            @endif
                          </label>
                        </div>
                      @empty
                        <p class="text-muted mb-0">
                          {{ __('m_tours.tour.ui.none.itinerary_items') }}
                        </p>
                      @endforelse
                    </div>
                  </div>
                </div>

                {{-- Lista ordenable de items del itinerario --}}
                <div class="col-md-6">
                  <div class="form-group">
                    <label>
                      <i class="fas fa-stream"></i> {{ __('m_tours.itinerary_item.ui.assigned_items') }}
                    </label>
                    <p class="text-muted small mb-2">
                      {{ __('m_tours.itinerary_item.ui.drag_to_order') }}
                    </p>

                    <div id="itinerary-items-sortable"
                         class="border rounded p-2 bg-dark"
                         style="min-height: 60px;">
                      {{-- Aquí se meten cards de items (existentes + nuevos) vía JS --}}
                    </div>
                  </div>
                </div>
              </div>

              <hr>

              {{-- Crear nuevos items inline (también caen en la lista ordenable) --}}
              <div class="form-group mt-2">
                <label>
                  <i class="fas fa-plus-circle"></i> {{ __('m_tours.itinerary_item.ui.register_item') }}
                </label>
                <p class="text-muted small mb-2">
                  {{ __('m_tours.itinerary_item.ui.register_item_hint') }}
                </p>

                <button type="button"
                        class="btn btn-sm btn-outline-primary"
                        id="btn-add-new-itinerary-item">
                  <i class="fas fa-plus"></i>
                  {{ __('m_tours.itinerary_item.ui.add_item') }}
                </button>
              </div>
            </div>
          </div>
        </div> {{-- /#new-itinerary-section --}}
      </div>
    </div>
  </div>

  {{-- Columna lateral de ayuda / resumen --}}
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
          <li>
            <strong>{{ __('m_tours.itinerary.ui.assign') }}:</strong>
            {{ __('m_tours.itinerary.ui.drag_hint') }}
          </li>
          <li>
            <strong>{{ __('m_tours.itinerary.ui.create_button') }}</strong>:
            {{ __('m_tours.itinerary.ui.create_title') }}
          </li>
        </ul>
        <hr>
        <h5>{{ __('m_tours.itinerary.ui.create_title') }}</h5>
        <ul class="small mb-0">
          <li>
            <strong>{{ __('m_tours.itinerary_item.ui.list_title') }}</strong>:
            {{ __('m_tours.itinerary.ui.drag_hint') }}
          </li>
          <li>
            <strong>{{ __('m_tours.itinerary_item.ui.register_item') }}</strong>:
            {{ __('m_tours.itinerary.ui.create_title') }}
          </li>
          <li>
            <strong>{{ __('m_tours.common.success_title') }}</strong>:
            {{ __('m_tours.itinerary.ui.save_changes') }}
          </li>
        </ul>
      </div>
    </div>

    @if($tour ?? false)
      <div class="card card-secondary mt-3">
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
            <p class="text-muted mb-0">
              {{ __('m_tours.tour.ui.none.itinerary') }}
            </p>
          @endif
        </div>
      </div>
    @endif
  </div>
</div>

@push('js')
  {{-- SortableJS para drag & drop de los ítems del itinerario --}}
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const itinerarySelect   = document.getElementById('select-itinerary');
    const newSection        = document.getElementById('new-itinerary-section');
    const viewSection       = document.getElementById('view-itinerary-items-create');
    const sortableContainer = document.getElementById('itinerary-items-sortable');
    const addNewBtn         = document.getElementById('btn-add-new-itinerary-item');
    const itineraryData     = @json($itineraryJson ?? []);

    let newItemCounter = 0;

    // === Utilidad: actualizar orden ===
    function updateItemOrders() {
      if (!sortableContainer) return;
      const cards = sortableContainer.querySelectorAll('.itinerary-sortable-item');
      cards.forEach((card, index) => {
        const orderInput = card.querySelector('input[name$="[order]"]');
        if (orderInput) {
          orderInput.value = index + 1;
        }
      });
    }

    // Activar drag & drop con SortableJS
    if (window.Sortable && sortableContainer) {
      Sortable.create(sortableContainer, {
        handle: '.drag-handle',
        animation: 150,
        onSort: updateItemOrders,
      });
    }

    // === Crear card de item EXISTENTE (checkbox pool) ===
    function createExistingItemCard(checkbox) {
      if (!sortableContainer) return;

      const itemId      = checkbox.value;
      const title       = checkbox.dataset.title || '';
      const description = checkbox.dataset.description || '';
      const key         = 'e_' + itemId;

      // evitar duplicados
      if (sortableContainer.querySelector(`.itinerary-sortable-item[data-key="${key}"]`)) {
        return;
      }

      const html = `
        <div class="card mb-2 itinerary-sortable-item" data-key="${key}" data-existing-id="${itemId}">
          <div class="card-body py-2 px-3 d-flex align-items-start">
            <span class="text-muted me-2 drag-handle" style="cursor: grab;">
              <i class="fas fa-grip-vertical"></i>
            </span>
            <div class="flex-grow-1">
              <strong>${title || @json(__('m_tours.itinerary_item.fields.title'))}</strong>
              ${description
                ? `<br><small class="text-muted">${description}</small>`
                : ''}
            </div>
            <button
              type="button"
              class="btn btn-sm btn-outline-danger ms-2 btn-remove-itinerary-item"
              data-key="${key}"
              aria-label="remove item"
            >
              <i class="fas fa-times"></i>
            </button>

            <input type="hidden"
                   name="new_itinerary[items][${key}][existing_item_id]"
                   value="${itemId}">
            <input type="hidden"
                   name="new_itinerary[items][${key}][title]"
                   value="${title}">
            <input type="hidden"
                   name="new_itinerary[items][${key}][description]"
                   value="${description}">
            <input type="hidden"
                   name="new_itinerary[items][${key}][order]"
                   value="0">
          </div>
        </div>
      `;

      sortableContainer.insertAdjacentHTML('beforeend', html);
      updateItemOrders();
    }

    // === Crear card de item NUEVO ===
    function createNewItemCard() {
      if (!sortableContainer) return;

      const key = 'n_' + newItemCounter++;
      const html = `
        <div class="card mb-2 itinerary-sortable-item" data-key="${key}">
          <div class="card-body py-2 px-3">
            <div class="d-flex align-items-start">
              <span class="text-muted me-2 drag-handle" style="cursor: grab;">
                <i class="fas fa-grip-vertical"></i>
              </span>
              <div class="flex-grow-1">
                <div class="mb-1">
                  <input type="text"
                         name="new_itinerary[items][${key}][title]"
                         class="form-control form-control-sm"
                         placeholder="{{ __('m_tours.itinerary_item.fields.title') }}"
                         required>
                </div>
                <div>
                  <input type="text"
                         name="new_itinerary[items][${key}][description]"
                         class="form-control form-control-sm"
                         placeholder="{{ __('m_tours.itinerary_item.fields.description') }}">
                </div>
              </div>
              <button
                type="button"
                class="btn btn-sm btn-outline-danger ms-2 btn-remove-itinerary-item"
                data-key="${key}"
                aria-label="remove item"
              >
                <i class="fas fa-times"></i>
              </button>
            </div>

            <input type="hidden"
                   name="new_itinerary[items][${key}][order]"
                   value="0">
          </div>
        </div>
      `;
      sortableContainer.insertAdjacentHTML('beforeend', html);
      updateItemOrders();
    }

    // === Manejar check / uncheck de pool de items existentes ===
    document.querySelectorAll('.existing-item-checkbox').forEach(cb => {
      cb.addEventListener('change', function () {
        if (!sortableContainer) return;

        const itemId = this.value;
        const key    = 'e_' + itemId;
        const card   = sortableContainer.querySelector(`.itinerary-sortable-item[data-key="${key}"]`);

        if (this.checked) {
          createExistingItemCard(this);
        } else if (card) {
          card.remove();
          updateItemOrders();
        }
      });
    });

    // === Botón para agregar item nuevo inline ===
    if (addNewBtn) {
      addNewBtn.addEventListener('click', function () {
        createNewItemCard();
      });
    }

    // === Eliminar item (existente o nuevo) desde la lista ordenable ===
    if (sortableContainer) {
      sortableContainer.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-remove-itinerary-item');
        if (!btn) return;

        const key  = btn.dataset.key;
        const card = sortableContainer.querySelector(`.itinerary-sortable-item[data-key="${key}"]`);

        if (card) {
          const existingId = card.getAttribute('data-existing-id');
          if (existingId) {
            const cb = document.getElementById('existing_item_' + existingId);
            if (cb) cb.checked = false;
          }
          card.remove();
          updateItemOrders();
        }
      });
    }

    // === Manejar selección de itinerario (existente vs nuevo) ===
    if (itinerarySelect) {
      itinerarySelect.addEventListener('change', function () {
        const value = this.value;

        if (!value) {
          // Crear nuevo
          if (newSection) newSection.style.display = 'block';
          if (viewSection) viewSection.style.display = 'none';
          return;
        }

        // Mostrar vista previa del itinerario seleccionado
        if (newSection) newSection.style.display = 'none';
        if (viewSection) viewSection.style.display = 'block';

        const data   = itineraryData[value] || null;
        const descEl = document.getElementById('selected-itinerary-description');
        const listEl = viewSection.querySelector('ul');

        if (data && descEl && listEl) {
          descEl.textContent = data.description || @json(__('m_tours.itinerary.fields.description_optional'));

          if (Array.isArray(data.items) && data.items.length) {
            listEl.innerHTML = data.items.map(item => `
                <li class="list-group-item">
                  <strong>${item.title || @json(__('m_tours.itinerary_item.fields.title'))}</strong>
                  ${item.description
                    ? `<br><small class="text-muted">${item.description}</small>`
                    : ''}
                </li>
              `).join('');
          } else {
            listEl.innerHTML = `
              <li class="list-group-item text-muted">
                {{ __('m_tours.tour.ui.none.itinerary_items') }}
              </li>`;
          }
        }
      });

      // Disparo inicial
      itinerarySelect.dispatchEvent(new Event('change'));
    }
  });
  </script>
@endpush
