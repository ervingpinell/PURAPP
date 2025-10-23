{{-- resources/views/admin/tours/create.blade.php --}}
@php
    $itineraryJson = $itineraries->keyBy('itinerary_id')->map(function ($it) {
        return [
            'description' => $it->description,
            'items' => $it->items->map(fn($item) => [
                'title' => $item->title,
                'description' => $item->description,
            ])->toArray()
        ];
    });
@endphp

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $err)
        <li>{{ $err }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">{{ __('m_tours.tour.ui.create_title') }}</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form id="formCrearTour" action="{{ route('admin.tours.store') }}" method="POST">
        @csrf
        <div class="modal-body">

          <div class="row g-4">
            <div class="col-lg-7">
              {{-- Nombre --}}
              <x-adminlte-input name="name" :label="__('m_tours.tour.fields.name')" value="{{ old('name') }}" required />

              {{-- ✅ SLUG (nuevo campo) --}}
              <div class="form-group">
                <label for="slug-create">
                  URL Amigable (Slug)
<small class="text-muted">{{ __('m_tours.tour.ui.slug_help') }}</small>
                </label>
                <div class="input-group">
                  <span class="input-group-text">/tours/</span>
                  <input type="text"
                         class="form-control @error('slug') is-invalid @enderror"
                         id="slug-create"
                         name="slug"
                         value="{{ old('slug') }}"
                         placeholder="minicombo-1">
                  <button type="button"
                          class="btn btn-outline-secondary"
                          onclick="generateSlugFromName()"
        title="{{ __('m_tours.tour.ui.generate_auto') }}">
                    <i class="fas fa-sync"></i>
                  </button>
                </div>
                @error('slug')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">
                  {{ __('m_tours.tour.ui.slug_preview_label') }}: <code id="slug-preview-create">{{ app()->getLocale() }}/tours/</code>
                </small>
              </div>

              {{-- Color / Viator / Duración --}}
              <div class="row">
                <div class="col-md-4">
                  <label class="form-label">{{ __('m_tours.tour.ui.color') }}</label>
                  <input type="color" name="color" class="form-control form-control-color"
                         value="{{ old('color', '#5cb85c') }}">
                </div>
                <div class="col-md-4">
                  <x-adminlte-input name="viator_code" :label="__('m_tours.tour.fields.viator_code') . ' (' . __('m_tours.itinerary.fields.description_optional') . ')'" value="{{ old('viator_code') }}" />
                </div>
                <div class="col-md-4">
                  <x-adminlte-input name="length" :label="__('m_tours.tour.fields.length_hours')" type="number" step="0.1"
                                    value="{{ old('length') }}" required />
                </div>
              </div>

              <x-adminlte-textarea name="overview" :label="__('m_tours.tour.fields.overview')" style="height:180px">{{ old('overview') }}</x-adminlte-textarea>

              <div class="row">
                <div class="col-md-4">
                  <x-adminlte-input name="adult_price" :label="__('m_tours.tour.fields.adult_price')" type="number" step="0.01" value="{{ old('adult_price') }}" required />
                </div>
                <div class="col-md-4">
                  <x-adminlte-input name="kid_price" :label="__('m_tours.tour.fields.kid_price')" type="number" step="0.01" value="{{ old('kid_price') }}" />
                </div>
                <div class="col-md-4">
                  <label class="form-label">{{ __('m_tours.tour.ui.default_capacity') }}</label>
                  <input type="number" name="max_capacity" class="form-control" value="{{ old('max_capacity', 12) }}" min="1" required>
                </div>
              </div>

              <x-adminlte-select name="tour_type_id" :label="__('m_tours.tour.fields.type')" required>
                <option value="">{{ __('m_tours.tour.ui.select_type') }}</option>
                @foreach($tourtypes as $type)
                  <option value="{{ $type->tour_type_id }}" @selected(old('tour_type_id') == $type->tour_type_id)>
                    {{ $type->name }}
                  </option>
                @endforeach
              </x-adminlte-select>

              <div class="mb-3">
                <label>{{ __('m_tours.tour.ui.available_languages') }}</label>
                <br>
                @foreach($languages as $lang)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="languages[]" value="{{ $lang->tour_language_id }}"
                           @checked(in_array($lang->tour_language_id, old('languages', [])))>
                    <label class="form-check-label">{{ $lang->name }}</label>
                  </div>
                @endforeach
              </div>

              <div class="mb-3">
                <label>{{ __('m_tours.tour.ui.amenities_included') }}</label>
                <br>
                @foreach($amenities as $am)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $am->amenity_id }}"
                           @checked(in_array($am->amenity_id, old('amenities', [])))>
                    <label class="form-check-label">{{ $am->name }}</label>
                  </div>
                @endforeach
              </div>

              <div class="mb-3">
                <label class="form-label text-danger">{{ __('m_tours.tour.ui.amenities_excluded') }}</label>
                <br>
                @foreach($amenities as $am)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="excluded_amenities[]" value="{{ $am->amenity_id }}"
                           @checked(in_array($am->amenity_id, old('excluded_amenities', [])))>
                    <label class="form-check-label">{{ $am->name }}</label>
                  </div>
                @endforeach
              </div>
            </div>

            <div class="col-lg-5">
              {{-- Itinerario --}}
              <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-bold">{{ __('m_tours.itinerary.fields.name') }}</div>
                <div class="card-body">
                  <select name="itinerary_id" id="select-itinerary" class="form-select" required>
                    <option value="">{{ __('m_tours.tour.ui.choose_itinerary') }}</option>
                    @foreach($itineraries as $it)
                      <option value="{{ $it->itinerary_id }}" @selected(old('itinerary_id') == $it->itinerary_id)>
                        {{ $it->name }}
                      </option>
                    @endforeach
                  </select>

                  <div id="itinerary-preview" class="mt-3" style="display:none;">
                    <div id="selected-itinerary-description" class="small text-muted mb-2" style="white-space: pre-line;"></div>
                    <ul class="list-group small" id="itinerary-items-list"></ul>
                  </div>
                </div>
              </div>

              {{-- Horarios --}}
              <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold d-flex align-items-center justify-content-between">
                  <span>{{ __('m_tours.tour.ui.schedules_title') }}</span>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <label class="form-label">{{ __('m_tours.tour.ui.use_existing_schedules') }}</label>
                    <select name="schedules_existing[]" class="form-select" multiple size="6">
                      @foreach($allSchedules as $sc)
                        @php
                          $start = \Carbon\Carbon::parse($sc->start_time)->format('H:i');
                          $end   = \Carbon\Carbon::parse($sc->end_time)->format('H:i');
                          $lbl   = $sc->label ? " - {$sc->label}" : '';
                        @endphp
                        <option value="{{ $sc->schedule_id }}">
                          {{ $start }} - {{ $end }}{{ $lbl }} ({{ __('m_tours.schedule.fields.max_capacity') }}: {{ $sc->max_capacity ?? '—' }})
                        </option>
                      @endforeach
                    </select>
                    <div class="form-text">{{ __('m_tours.tour.ui.multiple_hint_ctrl_cmd') }}</div>
                  </div>

                  <hr>

                  <label class="form-label">{{ __('m_tours.tour.ui.create_new_schedules') }}</label>
                  <div id="schedules-new-container">
                    <div class="row g-2 schedule-new mb-2">
                      <div class="col-4">
                        <input type="text" name="schedules_new[0][start_time]" class="form-control" placeholder="{{ __('m_tours.schedule.fields.start_time') }} (8:00 AM)">
                      </div>
                      <div class="col-4">
                        <input type="text" name="schedules_new[0][end_time]" class="form-control" placeholder="{{ __('m_tours.schedule.fields.end_time') }} (12:00 PM)">
                      </div>
                      <div class="col-4">
                        <input type="text" name="schedules_new[0][label]" class="form-control" placeholder="{{ __('m_tours.schedule.fields.label_optional') }}">
                      </div>
                      <div class="col-6 mt-2">
                        <input type="number" name="schedules_new[0][max_capacity]" class="form-control"
                               placeholder="{{ __('m_tours.schedule.fields.max_capacity') }} ({{ __('m_tours.tour.ui.empty_means_default') }})">
                      </div>
                      <div class="col-6 mt-2 text-end">
                        <button type="button" class="btn btn-outline-danger btn-sm btn-remove-sched">{{ __('m_tours.tour.ui.remove') }}</button>
                      </div>
                    </div>
                  </div>

                  <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-add-sched">+ {{ __('m_tours.tour.ui.add_schedule') }}</button>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_tours.tour.ui.cancel') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('m_tours.tour.ui.save') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('js')
<script>
// ✅ Función para generar slug desde el nombre
function generateSlugFromName() {
  const nameInput = document.querySelector('#modalRegistrar input[name="name"]');
  const slugInput = document.getElementById('slug-create');

  if (nameInput && slugInput) {
    const slug = nameInput.value
      .toLowerCase()
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // quita acentos
      .replace(/[^\w\s-]/g, '') // solo alfanuméricos, espacios y guiones
      .trim()
      .replace(/\s+/g, '-') // espacios a guiones
      .replace(/-+/g, '-'); // múltiples guiones a uno

    slugInput.value = slug;
    updateSlugPreviewCreate();
  }
}

// ✅ Actualizar preview
function updateSlugPreviewCreate() {
  const slugInput = document.getElementById('slug-create');
  const preview = document.getElementById('slug-preview-create');
  const locale = '{{ app()->getLocale() }}';

  if (slugInput && preview) {
    const slug = slugInput.value || '[auto]';
    preview.textContent = `/${locale}/tours/${slug}`;
  }
}

document.addEventListener('DOMContentLoaded', function () {
  // Preview de itinerario
  const itineraryData = @json($itineraryJson);
  const selIt = document.getElementById('select-itinerary');
  const prevBox = document.getElementById('itinerary-preview');
  const descBox = document.getElementById('selected-itinerary-description');
  const itemsUl = document.getElementById('itinerary-items-list');

  function refreshItineraryPreview(){
    const id = selIt.value;
    if (!id || !itineraryData[id]) {
      prevBox.style.display = 'none';
      return;
    }
    const data = itineraryData[id];
    descBox.textContent = data.description || '';
    itemsUl.innerHTML = data.items.length
      ? data.items.map(i => `<li class="list-group-item d-flex justify-content-between">
          <strong>${i.title}</strong><span class="text-muted ms-2">${i.description ?? ''}</span>
        </li>`).join('')
      : `<li class="list-group-item text-muted">{{ __('m_tours.itinerary.ui.no_items_assigned') }}</li>`;
    prevBox.style.display = 'block';
  }

  if (selIt) {
    selIt.addEventListener('change', refreshItineraryPreview);
    refreshItineraryPreview();
  }

  // Preview slug al escribir
  const slugInput = document.getElementById('slug-create');
  if (slugInput) {
    slugInput.addEventListener('input', updateSlugPreviewCreate);
  }

  // Horarios: añadir/eliminar
  const container = document.getElementById('schedules-new-container');
  const addBtn = document.getElementById('btn-add-sched');

  addBtn?.addEventListener('click', () => {
    const idx = container.querySelectorAll('.schedule-new').length;
    const row = document.createElement('div');
    row.className = 'row g-2 schedule-new mb-2';
    row.innerHTML = `
      <div class="col-4">
        <input type="text" name="schedules_new[${idx}][start_time]" class="form-control" placeholder="{{ __('m_tours.schedule.fields.start_time') }} (8:00 AM)">
      </div>
      <div class="col-4">
        <input type="text" name="schedules_new[${idx}][end_time]" class="form-control" placeholder="{{ __('m_tours.schedule.fields.end_time') }} (12:00 PM)">
      </div>
      <div class="col-4">
        <input type="text" name="schedules_new[${idx}][label]" class="form-control" placeholder="{{ __('m_tours.schedule.fields.label_optional') }}">
      </div>
      <div class="col-6 mt-2">
        <input type="number" name="schedules_new[${idx}][max_capacity]" class="form-control"
               placeholder="{{ __('m_tours.schedule.fields.max_capacity') }} ({{ __('m_tours.tour.ui.empty_means_default') }})">
      </div>
      <div class="col-6 mt-2 text-end">
        <button type="button" class="btn btn-outline-danger btn-sm btn-remove-sched">{{ __('m_tours.tour.ui.remove') }}</button>
      </div>
    `;
    container.appendChild(row);
  });

  container?.addEventListener('click', (e) => {
    if (e.target.closest('.btn-remove-sched')) {
      const rows = container.querySelectorAll('.schedule-new');
      if (rows.length > 1) e.target.closest('.schedule-new').remove();
    }
  });
});
</script>
@endpush
