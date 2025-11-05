{{-- resources/views/admin/tours/partials/tab-itinerary.blade.php --}}
<div class="itinerary-tab"> {{-- wrapper seguro para no romper el DOM del card/tab --}}

  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            {{ __('m_tours.itinerary.ui.page_heading') }}
          </h3>
        </div>

        <div class="card-body">

          {{-- Selector de Itinerario Existente / Crear Nuevo --}}
          <div class="form-group">
            <label for="itinerary_id">{{ __('m_tours.tour.fields.itinerary') }}</label>
            <select
              name="itinerary_id"
              id="itinerary_id"
              class="form-control @error('itinerary_id') is-invalid @enderror"
            >
              <option value="">{{ __('m_tours.itinerary.ui.new_itinerary') }}</option>
              @foreach($itineraries ?? [] as $itinerary)
                <option value="{{ $itinerary->itinerary_id }}"
                  {{ old('itinerary_id', $tour->itinerary_id ?? '') == $itinerary->itinerary_id ? 'selected' : '' }}>
                  {{ $itinerary->name ?? __('m_tours.itinerary.fields.name') . ' #' . $itinerary->itinerary_id }}
                </option>
              @endforeach
            </select>
            @error('itinerary_id')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>

          {{-- Vista de Itinerario Existente --}}
          <div id="existing-itinerary-view" style="display:none;">
            <div class="alert alert-info">
              <strong>{{ __('m_tours.itinerary.fields.description') }}:</strong>
              <p id="itinerary-description" class="mb-0 mt-2"></p>
            </div>

            <div class="card mb-0">
              <div class="card-header">
                <h4 class="card-title mb-0">{{ __('m_tours.itinerary_item.ui.list_title') }}</h4>
              </div>
              <div class="card-body p-0">
                <ul id="itinerary-items-list" class="list-group list-group-flush"></ul>
              </div>
            </div>
          </div>

          {{-- Formulario para Crear Nuevo Itinerario --}}
          <div id="new-itinerary-form" style="display:none;">

            {{-- Nombre --}}
            <div class="form-group">
              <label for="new_itinerary_name">
                {{ __('m_tours.itinerary.fields.name') }} <span class="text-danger">*</span>
              </label>
              <input
                type="text"
                name="new_itinerary[name]"
                id="new_itinerary_name"
                class="form-control"
                placeholder="{{ __('m_tours.itinerary.ui.create_title') }}"
                value="{{ old('new_itinerary.name') }}"
              >
              <small class="form-text text-muted">
                {{ __('m_tours.itinerary.fields.name') }}
              </small>
            </div>

            {{-- Descripción --}}
            <div class="form-group">
              <label for="new_itinerary_description">{{ __('m_tours.itinerary.fields.description') }}</label>
              <textarea
                name="new_itinerary[description]"
                id="new_itinerary_description"
                class="form-control"
                rows="3"
                placeholder="{{ __('m_tours.itinerary.fields.description_optional') }}"
              >{{ old('new_itinerary.description') }}</textarea>
            </div>

            {{-- Seleccionar ítems existentes --}}
            <div class="card">
              <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                  <i class="fas fa-list"></i>
                  {{ __('m_tours.itinerary_item.ui.list_title') }}
                </h5>
              </div>
              <div class="card-body">
                <div class="alert alert-warning">
                  <i class="fas fa-hand-pointer"></i>
                  <strong>{{ __('m_tours.itinerary.ui.assign') }}:</strong>
                  {{ __('m_tours.itinerary.ui.drag_hint') }}
                </div>

                @php
                  $availableItems = \App\Models\ItineraryItem::where('is_active', true)
                    ->orderBy('title')->get();
                @endphp

                @if($availableItems->isNotEmpty())
                  <ul class="list-group sortable-items-new" id="sortable-new-itinerary">
                    @foreach($availableItems as $item)
                      <li class="list-group-item d-flex justify-content-between align-items-center" data-id="{{ $item->item_id }}">
                        <div class="form-check">
                          <input
                            type="checkbox"
                            class="form-check-input checkbox-item-new"
                            value="{{ $item->item_id }}"
                            id="new-item-{{ $item->item_id }}"
                            {{ in_array($item->item_id, old('new_itinerary.existing_items', [])) ? 'checked' : '' }}
                          >
                          <label class="form-check-label" for="new-item-{{ $item->item_id }}">
                            <strong>{{ $item->title }}</strong>
                            @if($item->description)
                              <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($item->description, 50) }}</small>
                            @endif
                          </label>
                        </div>
                        <i class="fas fa-arrows-alt handle text-muted" style="cursor: move;" title="{{ __('m_tours.itinerary.ui.drag_handle') }}"></i>
                      </li>
                    @endforeach
                  </ul>
                  <div id="ordered-new-itinerary-inputs"></div>
                @else
                  <p class="text-muted mb-0">
                    {{ __('m_tours.itinerary_item.ui.list_title') }} — {{ __('m_tours.tour.ui.none.itinerary_items') }}
                    <a href="{{ route('admin.tours.itinerary.index') }}" target="_blank">
                      {{ __('m_tours.itinerary.ui.new_itinerary') }}
                    </a>
                  </p>
                @endif
              </div>
            </div>

            {{-- Crear ítems nuevos --}}
            <div class="card mt-3">
              <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                  <i class="fas fa-plus-circle"></i>
                  {{ __('m_tours.itinerary_item.ui.register_item') }}
                </h5>
                <button type="button" class="btn btn-sm btn-light" id="add-itinerary-item">
                  <i class="fas fa-plus"></i> {{ __('m_tours.itinerary_item.ui.add_item') }}
                </button>
              </div>
              <div class="card-body">
                <div id="itinerary-items-container">
                  @if(old('new_itinerary.items'))
                    @foreach(old('new_itinerary.items') as $index => $item)
                      <div class="itinerary-item mb-3 p-3 border rounded">
                        <div class="row">
                          <div class="col-md-11">
                            <div class="form-group">
                              <label>{{ __('m_tours.itinerary_item.fields.title') }}</label>
                              <input type="text" name="new_itinerary[items][{{ $index }}][title]" class="form-control"
                                     value="{{ $item['title'] ?? '' }}" placeholder="{{ __('m_tours.itinerary_item.fields.title') }}">
                            </div>
                            <div class="form-group mb-0">
                              <label>{{ __('m_tours.itinerary_item.fields.description') }}</label>
                              <textarea name="new_itinerary[items][{{ $index }}][description]" class="form-control" rows="2"
                                        placeholder="{{ __('m_tours.itinerary_item.fields.description') }}">{{ $item['description'] ?? '' }}</textarea>
                            </div>
                          </div>
                          <div class="col-md-1 d-flex align-items-center">
                            <button type="button" class="btn btn-danger btn-sm remove-itinerary-item" aria-label="remove item">
                              <i class="fas fa-trash"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  @endif
                </div>

                @if(!old('new_itinerary.items'))
                  <div class="text-muted text-center py-3" id="empty-items-message">
                    <i class="fas fa-info-circle"></i>
                    {{ __('m_tours.itinerary_item.ui.add_item') }}
                  </div>
                @endif
              </div>
            </div>

          </div> {{-- /#new-itinerary-form --}}
        </div> {{-- /.card-body --}}
      </div> {{-- /.card --}}
    </div> {{-- /.col-md-8 --}}

    {{-- Columna lateral: Ayuda + Resumen --}}
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
              <h5>{{ $tour->itinerary->name ?? __('m_tours.itinerary.fields.name') }}</h5>
              <p class="text-muted small">{{ $tour->itinerary->description }}</p>

              @if($tour->itinerary->items->isNotEmpty())
                <ol class="pl-3 mb-0">
                  @foreach($tour->itinerary->items as $item)
                    <li><strong>{{ $item->title }}</strong></li>
                  @endforeach
                </ol>
              @endif
            @else
              <p class="text-muted mb-0">{{ __('m_tours.tour.ui.none.itinerary') }}</p>
            @endif
          </div>
        </div>
      @endif
    </div> {{-- /.col-md-4 --}}
  </div> {{-- /.row --}}

  {{-- Template para nuevos ítems (crear desde cero) --}}
  <template id="itinerary-item-template">
    <div class="itinerary-item mb-3 p-3 border rounded">
      <div class="row">
        <div class="col-md-11">
          <div class="form-group">
            <label>{{ __('m_tours.itinerary_item.fields.title') }}</label>
            <input type="text" name="new_itinerary[items][__INDEX__][title]" class="form-control"
                   placeholder="{{ __('m_tours.itinerary_item.fields.title') }}">
          </div>
          <div class="form-group mb-0">
            <label>{{ __('m_tours.itinerary_item.fields.description') }}</label>
            <textarea name="new_itinerary[items][__INDEX__][description]" class="form-control" rows="2"
                      placeholder="{{ __('m_tours.itinerary_item.fields.description') }}"></textarea>
          </div>
        </div>
        <div class="col-md-1 d-flex align-items-center">
          <button type="button" class="btn btn-danger btn-sm remove-itinerary-item" aria-label="remove item">
            <i class="fas fa-trash"></i>
          </button>
        </div>
      </div>
    </div>
  </template>

</div> {{-- /.itinerary-tab --}}

@push('css')
<style>
  .sortable-items-new { list-style: none; padding: 0; margin: 0; }
  .sortable-items-new .handle { cursor: move; }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const itinerarySelect = document.getElementById('itinerary_id');
  const existingView    = document.getElementById('existing-itinerary-view');
  const newForm         = document.getElementById('new-itinerary-form');
  const descriptionEl   = document.getElementById('itinerary-description');
  const itemsList       = document.getElementById('itinerary-items-list');
  const itineraryData   = @json($itineraryJson ?? []);

  function updateItineraryView() {
    if (!itinerarySelect) return;
    const selectedValue = itinerarySelect.value;

    if (!selectedValue) {
      existingView.style.display = 'none';
      newForm.style.display = 'block';
    } else {
      existingView.style.display = 'block';
      newForm.style.display = 'none';

      const data = itineraryData[selectedValue] || {};
      descriptionEl.textContent = data.description || '{{ __('m_tours.itinerary.fields.description_optional') }}';

      if (Array.isArray(data.items) && data.items.length > 0) {
        itemsList.innerHTML = data.items.map(item => `
          <li class="list-group-item">
            <strong>${item.title || @json(__('m_tours.itinerary_item.fields.title'))}</strong>
            <p class="mb-0 text-muted small">${item.description || ''}</p>
          </li>
        `).join('');
      } else {
        itemsList.innerHTML = `
          <li class="list-group-item text-muted">
            {{ __('m_tours.tour.ui.none.itinerary_items') }}
          </li>
        `;
      }
    }
  }

  if (itinerarySelect) {
    itinerarySelect.addEventListener('change', updateItineraryView);
    updateItineraryView();
  }

  const sortableList = document.getElementById('sortable-new-itinerary');
  if (sortableList) {
    new Sortable(sortableList, { animation: 150, handle: '.handle' });
  }

  const tourForm = document.getElementById('tourForm');
  function buildOrderedInputs() {
    const container = document.getElementById('ordered-new-itinerary-inputs');
    if (!container || !sortableList) return;

    container.innerHTML = '';
    let index = 0;
    sortableList.querySelectorAll('li').forEach(li => {
      const checkbox = li.querySelector('.checkbox-item-new');
      if (checkbox && checkbox.checked) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `new_itinerary[existing_items][${index}]`;
        input.value = checkbox.value;
        container.appendChild(input);
        index++;
      }
    });
  }

  if (tourForm) {
    tourForm.addEventListener('submit', function () {
      if (itinerarySelect && itinerarySelect.value === '') {
        buildOrderedInputs();
      }
    });
  }

  let itemIndex = {{ old('new_itinerary.items') ? count(old('new_itinerary.items')) : 0 }};
  const addBtn = document.getElementById('add-itinerary-item');
  const tpl    = document.getElementById('itinerary-item-template');
  const cont   = document.getElementById('itinerary-items-container');

  addBtn?.addEventListener('click', function () {
    if (!tpl || !cont) return;
    const html = tpl.innerHTML.replace(/__INDEX__/g, itemIndex++);
    cont.insertAdjacentHTML('beforeend', html);
    const empty = document.getElementById('empty-items-message');
    if (empty) empty.style.display = 'none';
  });

  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.remove-itinerary-item');
    if (!btn) return;
    e.preventDefault();
    const block = btn.closest('.itinerary-item');
    if (block) block.remove();
    const container = document.getElementById('itinerary-items-container');
    const empty = document.getElementById('empty-items-message');
    if (container && container.children.length === 0 && empty) {
      empty.style.display = 'block';
    }
  });
});
</script>
@endpush
