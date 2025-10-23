{{-- resources/views/admin/tours/edit.blade.php --}}
@php use Carbon\Carbon; @endphp

@foreach($tours as $tour)
<div class="modal fade" id="modalEditar{{ $tour->tour_id }}" tabindex="-1" aria-labelledby="modalEditarLabel{{ $tour->tour_id }}" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title" id="modalEditarLabel{{ $tour->tour_id }}">{{ __('m_tours.tour.ui.edit_title') }} #{{ $tour->tour_id }}</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('m_tours.tour.ui.close') }}"></button>
      </div>

      <form action="{{ route('admin.tours.update', $tour->tour_id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-body">

          @if($errors->any() && session('showEditModal') == $tour->tour_id)
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="row g-4">
            <div class="col-lg-7">

              {{-- Nombre --}}
              <x-adminlte-input name="name" :label="__('m_tours.tour.fields.name')" value="{{ old('name', $tour->name) }}" required />

              {{-- ✅ SLUG (nuevo campo) --}}
              <div class="form-group">
                <label for="slug-{{ $tour->tour_id }}">
                  URL (Slug)
<small class="text-muted">{{ __('m_tours.tour.ui.slug_help') }}</small>
                </label>
                <div class="input-group">
                  <span class="input-group-text">/tours/</span>
                  <input type="text"
                         class="form-control @error('slug') is-invalid @enderror"
                         id="slug-{{ $tour->tour_id }}"
                         name="slug"
                         value="{{ old('slug', $tour->slug) }}"
                         placeholder="minicombo-1">
                  <button type="button"
                          class="btn btn-outline-secondary btn-slug-auto"
                          data-tour-id="{{ $tour->tour_id }}"
        title="{{ __('m_tours.tour.ui.generate_auto') }}">
                    <i class="fas fa-sync"></i>
                  </button>
                </div>
                @error('slug')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">
                  {{ __('m_tours.tour.ui.slug_preview_label') }}: <code id="slug-preview-{{ $tour->tour_id }}">{{ localized_route('tours.show', parameters: $tour->slug ?: $tour->tour_id) }}</code>
                </small>
              </div>

              {{-- Color / Viator / Duración --}}
              <div class="row">
                <div class="col-md-4">
                  <label class="form-label">{{ __('m_tours.tour.ui.color') }}</label>
                  <input type="color" name="color" class="form-control form-control-color"
                         value={{ old('color', $tour->color ?? '#5cb85c') }}>
                </div>
                <div class="col-md-4">
                  <x-adminlte-input name="viator_code" :label="__('m_tours.tour.fields.viator_code') . ' (' . __('m_tours.itinerary.fields.description_optional') . ')'"
                                    value="{{ old('viator_code', $tour->viator_code) }}" />
                </div>
                <div class="col-md-4">
                  <x-adminlte-input name="length" :label="__('m_tours.tour.fields.length_hours')" type="number" step="0.1"
                                    value="{{ old('length', $tour->length) }}" required />
                </div>
              </div>

              {{-- Overview --}}
              <x-adminlte-textarea name="overview" :label="__('m_tours.tour.fields.overview')" style="height:180px">
                {{ old('overview', $tour->overview) }}
              </x-adminlte-textarea>

              {{-- Precios + Cupo por defecto --}}
              <div class="row">
                <div class="col-md-4">
                  <x-adminlte-input name="adult_price" :label="__('m_tours.tour.fields.adult_price')" type="number" step="0.01"
                                    value="{{ old('adult_price', $tour->adult_price) }}" required />
                </div>
                <div class="col-md-4">
                  <x-adminlte-input name="kid_price" :label="__('m_tours.tour.fields.kid_price')" type="number" step="0.01"
                                    value="{{ old('kid_price', $tour->kid_price) }}" />
                </div>
                <div class="col-md-4">
                  <label class="form-label">{{ __('m_tours.tour.ui.default_capacity') }}</label>
                  <input type="number" name="max_capacity" class="form-control"
                         value="{{ old('max_capacity', $tour->max_capacity) }}" min="1" required>
                </div>
              </div>

              {{-- Tipo --}}
              <x-adminlte-select name="tour_type_id" :label="__('m_tours.tour.fields.type')" required>
                <option value="">{{ __('m_tours.tour.ui.select_type') }}</option>
                @foreach($tourtypes as $type)
                  <option value="{{ $type->tour_type_id }}"
                          @selected(old('tour_type_id', $tour->tour_type_id) == $type->tour_type_id)>
                    {{ $type->name }}
                  </option>
                @endforeach
              </x-adminlte-select>

              {{-- Idiomas --}}
              <div class="mb-3">
                <label>{{ __('m_tours.tour.ui.available_languages') }}</label>
                <br>
                @php $langsChecked = old('languages', $tour->languages->pluck('tour_language_id')->toArray()); @endphp
                @foreach($languages as $lang)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="languages[]" value="{{ $lang->tour_language_id }}"
                           @checked(in_array($lang->tour_language_id, $langsChecked))>
                    <label class="form-check-label">{{ $lang->name }}</label>
                  </div>
                @endforeach
              </div>

              {{-- Amenidades incluidas --}}
              <div class="mb-3">
                <label>{{ __('m_tours.tour.ui.amenities_included') }}</label>
                <br>
                @php $amsChecked = old('amenities', $tour->amenities->pluck('amenity_id')->toArray()); @endphp
                @foreach($amenities as $am)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $am->amenity_id }}"
                           @checked(in_array($am->amenity_id, $amsChecked))>
                    <label class="form-check-label">{{ $am->name }}</label>
                  </div>
                @endforeach
              </div>

              {{-- Amenidades NO incluidas --}}
              <div class="mb-3">
                <label class="form-label text-danger">{{ __('m_tours.tour.ui.amenities_excluded') }}</label>
                <br>
                @php $exChecked = old('excluded_amenities', $tour->excludedAmenities->pluck('amenity_id')->toArray()); @endphp
                @foreach($amenities as $am)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="excluded_amenities[]" value="{{ $am->amenity_id }}"
                           @checked(in_array($am->amenity_id, $exChecked))>
                    <label class="form-check-label">{{ $am->name }}</label>
                  </div>
                @endforeach
              </div>
            </div>

            {{-- Columna derecha --}}
            <div class="col-lg-5">

              {{-- Itinerario --}}
              <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-bold">{{ __('m_tours.itinerary.fields.name') }}</div>
                <div class="card-body">
                  <select name="itinerary_id" id="edit-itinerary-{{ $tour->tour_id }}" class="form-select" required>
                    <option value="">{{ __('m_tours.tour.ui.choose_itinerary') }}</option>
                    @foreach($itineraries as $it)
                      <option value="{{ $it->itinerary_id }}"
                              @selected(old('itinerary_id', $tour->itinerary_id) == $it->itinerary_id)>
                        {{ $it->name }}
                      </option>
                    @endforeach
                  </select>

                  <div id="itinerary-preview-{{ $tour->tour_id }}" class="mt-3" style="display:none;">
                    <div id="selected-itinerary-description-{{ $tour->tour_id }}"
                         class="small text-muted mb-2" style="white-space: pre-line;"></div>
                    <ul class="list-group small" id="itinerary-items-list-{{ $tour->tour_id }}"></ul>
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
                    @php
                      $preSelected = collect(old('schedules_existing', $tour->schedules->pluck('schedule_id')))
                                      ->map(fn($v)=>(int)$v)->all();
                    @endphp
                    <select class="form-select" name="schedules_existing[]" multiple size="6">
                      @foreach($allSchedules as $sc)
                        @php
                          $start = Carbon::parse($sc->start_time)->format('H:i');
                          $end   = Carbon::parse($sc->end_time)->format('H:i');
                          $lbl   = $sc->label ? " - {$sc->label}" : '';
                          $cap   = $sc->max_capacity ?? '—';
                        @endphp
                        <option value="{{ $sc->schedule_id }}" @selected(in_array($sc->schedule_id, $preSelected, true))>
                          {{ $start }} - {{ $end }}{{ $lbl }} ({{ __('m_tours.schedule.fields.max_capacity') }}: {{ $cap }})
                        </option>
                      @endforeach
                    </select>
                    <div class="form-text">{{ __('m_tours.tour.ui.multiple_hint_ctrl_cmd') }}</div>
                  </div>

                  <hr>

                  <label class="form-label">{{ __('m_tours.tour.ui.create_new_schedules') }}</label>
                  <div id="edit-new-schedules-{{ $tour->tour_id }}">
                    <div class="schedule-row border-bottom pb-3 mb-3 d-none" id="edit-row-template-{{ $tour->tour_id }}">
                      <div class="row g-2 align-items-end">
                        <div class="col-4">
                          <label class="form-label">{{ __('m_tours.schedule.fields.start_time') }}</label>
                          <input type="text" class="form-control sch-start" placeholder="{{ __('m_tours.schedule.fields.start_time') }} (8:00 AM)">
                        </div>
                        <div class="col-4">
                          <label class="form-label">{{ __('m_tours.schedule.fields.end_time') }}</label>
                          <input type="text" class="form-control sch-end" placeholder="{{ __('m_tours.schedule.fields.end_time') }} (12:00 PM)">
                        </div>
                        <div class="col-4">
                          <label class="form-label">{{ __('m_tours.schedule.fields.label_optional') }}</label>
                          <input type="text" class="form-control sch-label" placeholder="{{ __('m_tours.schedule.fields.label') }}">
                        </div>
                        <div class="col-6 mt-2">
                          <input type="number" min="1" class="form-control sch-cap"
                                 placeholder="{{ __('m_tours.schedule.fields.max_capacity') }} ({{ __('m_tours.tour.ui.empty_means_default') }})">
                        </div>
                        <div class="col-6 mt-2 text-end">
                          <button type="button" class="btn btn-outline-danger btn-sm btn-remove-row">{{ __('m_tours.tour.ui.remove') }}</button>
                        </div>
                      </div>
                    </div>

                    @php $oldRows = old('schedules_new', []); @endphp
                    @foreach($oldRows as $i => $r)
                      <div class="schedule-row border-bottom pb-3 mb-3">
                        <div class="row g-2 align-items-end">
                          <div class="col-4">
                            <label class="form-label">{{ __('m_tours.schedule.fields.start_time') }}</label>
                            <input type="text" name="schedules_new[{{ $i }}][start_time]" class="form-control"
                                   value="{{ $r['start_time'] ?? '' }}" placeholder="{{ __('m_tours.schedule.fields.start_time') }} (8:00 AM)">
                          </div>
                          <div class="col-4">
                            <label class="form-label">{{ __('m_tours.schedule.fields.end_time') }}</label>
                            <input type="text" name="schedules_new[{{ $i }}][end_time]" class="form-control"
                                   value="{{ $r['end_time'] ?? '' }}" placeholder="{{ __('m_tours.schedule.fields.end_time') }} (12:00 PM)">
                          </div>
                          <div class="col-4">
                            <label class="form-label">{{ __('m_tours.schedule.fields.label_optional') }}</label>
                            <input type="text" name="schedules_new[{{ $i }}][label]" class="form-control"
                                   value="{{ $r['label'] ?? '' }}">
                          </div>
                          <div class="col-6 mt-2">
                            <input type="number" name="schedules_new[{{ $i }}][max_capacity]" class="form-control"
                                   min="1" value="{{ $r['max_capacity'] ?? '' }}" placeholder="{{ __('m_tours.schedule.fields.max_capacity') }} ({{ __('m_tours.tour.ui.empty_means_default') }})">
                          </div>
                          <div class="col-6 mt-2 text-end">
                            <button type="button" class="btn btn-outline-danger btn-sm btn-remove-row">{{ __('m_tours.tour.ui.remove') }}</button>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>

                  <button
                    type="button"
                    class="btn btn-outline-secondary btn-sm btn-add-schedule-row"
                    data-target="#edit-new-schedules-{{ $tour->tour_id }}"
                    data-template="#edit-row-template-{{ $tour->tour_id }}">
                    + {{ __('m_tours.tour.ui.add_schedule') }}
                  </button>

                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_tours.tour.ui.cancel') }}</button>
          <button type="submit" class="btn btn-warning">{{ __('m_tours.tour.ui.update') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

@push('js')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // ✅ Auto-generar slug desde el nombre
  document.querySelectorAll('.btn-slug-auto').forEach(btn => {
    btn.addEventListener('click', () => {
      const tourId = btn.dataset.tourId;
      const nameInput = document.querySelector(`#modalEditar${tourId} input[name="name"]`);
      const slugInput = document.querySelector(`#slug-${tourId}`);

      if (nameInput && slugInput) {
        const slug = nameInput.value
          .toLowerCase()
          .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // quita acentos
          .replace(/[^\w\s-]/g, '') // solo alfanuméricos, espacios y guiones
          .trim()
          .replace(/\s+/g, '-') // espacios a guiones
          .replace(/-+/g, '-'); // múltiples guiones a uno

        slugInput.value = slug;
        updateSlugPreview(tourId);
      }
    });
  });

  // ✅ Actualizar preview del slug
  function updateSlugPreview(tourId) {
    const slugInput = document.querySelector(`#slug-${tourId}`);
    const preview = document.querySelector(`#slug-preview-${tourId}`);
    const locale = '{{ app()->getLocale() }}';

    if (slugInput && preview) {
      const slug = slugInput.value || tourId;
      preview.textContent = `/${locale}/tours/${slug}`;
    }
  }

  document.querySelectorAll('[id^="slug-"]').forEach(input => {
    if (input.id.startsWith('slug-')) {
      const tourId = input.id.replace('slug-', '');
      input.addEventListener('input', () => updateSlugPreview(tourId));
    }
  });
});
</script>
@endpush
