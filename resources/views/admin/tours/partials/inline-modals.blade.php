{{-- ===== Modales: crear entidad (categoría, idioma, amenidad, horario) ===== --}}

{{-- Modal: Crear Categoría --}}
<div class="modal fade" id="modalCreateCategory" tabindex="-1" aria-labelledby="modalCreateCategoryLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="modalCreateCategoryLabel" class="modal-title">{{ __('m_tours.tour.modal.create_category') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('m_tours.common.close') }}"></button>
      </div>
      <div class="modal-body">
        <form id="formCreateCategory" novalidate>
          <div class="mb-3">
            <label class="form-label">{{ __('m_tours.tour.modal.fields.name') }} *</label>
            <input type="text" name="name" class="form-control" required>
            <div class="invalid-feedback">{{ __('m_tours.common.required') }}</div>
          </div>

          <div class="mb-3">
            <label class="form-label">{{ __('m_tours.tour.modal.fields.age_range') }}</label>
            <input type="text" name="age_range" class="form-control" placeholder="{{ __('m_tours.tour.modal.placeholders.age_range') }}">
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">{{ __('m_tours.tour.modal.fields.min') }} *</label>
              <input type="number" name="min_quantity" class="form-control" value="0" min="0" required>
              <div class="invalid-feedback">{{ __('m_tours.common.required') }}</div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">{{ __('m_tours.tour.modal.fields.max') }} *</label>
              <input type="number" name="max_quantity" class="form-control" value="12" min="0" required>
              <div class="invalid-feedback">{{ __('m_tours.common.required') }}</div>
            </div>
          </div>
          <div class="small text-muted">{{ __('m_tours.tour.modal.hints.min_le_max') }}</div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_tours.common.cancel') }}</button>
        <button type="button" class="btn btn-primary" onclick="submitCreateCategory()">
          <i class="fas fa-save"></i> {{ __('m_tours.common.create') }}
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Modal: Crear Idioma --}}
<div class="modal fade" id="modalCreateLanguage" tabindex="-1" aria-labelledby="modalCreateLanguageLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="modalCreateLanguageLabel" class="modal-title">{{ __('m_tours.tour.modal.create_language') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('m_tours.common.close') }}"></button>
      </div>
      <div class="modal-body">
        <form id="formCreateLanguage" novalidate>
          <div class="mb-3">
            <label class="form-label">{{ __('m_tours.language.fields.name') }} *</label>
            <input type="text" name="name" class="form-control" required>
            <div class="invalid-feedback">{{ __('m_tours.common.required') }}</div>
          </div>
          <div class="mb-1">
            <label class="form-label">{{ __('m_tours.language.fields.code') }} *</label>
            <input type="text" name="code" class="form-control" placeholder="es" maxlength="5" required>
            <div class="invalid-feedback">{{ __('m_tours.common.required') }}</div>
          </div>
          <small class="form-text text-muted">{{ __('m_tours.language.hints.iso_639_1') }}</small>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_tours.common.cancel') }}</button>
        <button type="button" class="btn btn-primary" onclick="submitCreateLanguage()">
          <i class="fas fa-save"></i> {{ __('m_tours.common.create') }}
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Modal: Crear Amenidad --}}
<div class="modal fade" id="modalCreateAmenity" tabindex="-1" aria-labelledby="modalCreateAmenityLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="modalCreateAmenityLabel" class="modal-title">{{ __('m_tours.tour.modal.create_amenity') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('m_tours.common.close') }}"></button>
      </div>
      <div class="modal-body">
        <form id="formCreateAmenity" novalidate>
          <div class="mb-3">
            <label class="form-label">{{ __('m_tours.amenity.fields.name') }} *</label>
            <input type="text" name="name" class="form-control" required>
            <div class="invalid-feedback">{{ __('m_tours.common.required') }}</div>
          </div>
          <div class="mb-1">
            <label class="form-label">{{ __('m_tours.amenity.fields.icon') }}</label>
            <input type="text" name="icon" class="form-control" value="fas fa-check" placeholder="fas fa-check">
          </div>
          <small class="form-text text-muted">{{ __('m_tours.amenity.hints.fontawesome') }}</small>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_tours.common.cancel') }}</button>
        <button type="button" class="btn btn-primary" onclick="submitCreateAmenity()">
          <i class="fas fa-save"></i> {{ __('m_tours.common.create') }}
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Modal: Crear Horario --}}
<div class="modal fade" id="modalCreateSchedule" tabindex="-1" aria-labelledby="modalCreateScheduleLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="modalCreateScheduleLabel" class="modal-title">{{ __('m_tours.tour.modal.create_schedule') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('m_tours.common.close') }}"></button>
      </div>
      <div class="modal-body">
        <form id="formCreateSchedule" novalidate>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">{{ __('m_tours.schedule.fields.start_time') }} *</label>
              <input type="time" name="start_time" class="form-control" required>
              <div class="invalid-feedback">{{ __('m_tours.common.required') }}</div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">{{ __('m_tours.schedule.fields.end_time') }} *</label>
              <input type="time" name="end_time" class="form-control" required>
              <div class="invalid-feedback">{{ __('m_tours.common.required') }}</div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('m_tours.schedule.fields.label_optional') }}</label>
            <input type="text" name="label" class="form-control" placeholder="{{ __('m_tours.schedule.placeholders.morning') }}">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_tours.common.cancel') }}</button>
        <button type="button" class="btn btn-primary" onclick="submitCreateSchedule()">
          <i class="fas fa-save"></i> {{ __('m_tours.common.create') }}
        </button>
      </div>
    </div>
  </div>
</div>

{{-- (Opcional) Modal: Crear Itinerario - si lo usas en esta vista --}}
<div class="modal fade" id="modalCreateItinerary" tabindex="-1" aria-labelledby="modalCreateItineraryLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="modalCreateItineraryLabel" class="modal-title">{{ __('m_tours.itinerary.modal.create_itinerary') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('m_tours.common.close') }}"></button>
      </div>
      <div class="modal-body">
        <form id="formCreateItinerary">
          <div class="mb-3">
            <label class="form-label">{{ __('m_tours.itinerary.fields.name') }} *</label>
            <input type="text" name="name" class="form-control" required>
            <div class="invalid-feedback">{{ __('m_tours.common.required') }}</div>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('m_tours.itinerary.fields.description') }}</label>
            <textarea name="description" class="form-control" rows="2"></textarea>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">{{ __('m_tours.itinerary.fields.items') }}</h6>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItineraryItem()">
              <i class="fas fa-plus"></i> {{ __('m_tours.common.add') }}
            </button>
          </div>

          <div id="itinerary-items-container"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_tours.common.cancel') }}</button>
        <button type="button" class="btn btn-primary" onclick="submitCreateItinerary()">
          <i class="fas fa-save"></i> {{ __('m_tours.common.create') }}
        </button>
      </div>
    </div>
  </div>
</div>

<script>
let itineraryItemCount = 0;

function addItineraryItem() {
  const container = document.getElementById('itinerary-items-container');
  if (!container) return;

  const html = `
    <div class="card mb-2 itinerary-item-card">
      <div class="card-body">
        <div class="row g-2">
          <div class="col-md-10">
            <div class="mb-2">
              <input type="text"
                     name="items[${itineraryItemCount}][title]"
                     class="form-control form-control-sm"
                     placeholder="{{ __('m_tours.itinerary.fields.item_title') }}"
                     required>
              <div class="invalid-feedback">{{ __('m_tours.common.required') }}</div>
            </div>
            <div>
              <input type="text"
                     name="items[${itineraryItemCount}][description]"
                     class="form-control form-control-sm"
                     placeholder="{{ __('m_tours.itinerary.fields.item_description') }}">
            </div>
          </div>
          <div class="col-md-2 text-end">
            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.itinerary-item-card').remove()">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>
      </div>
    </div>`;
  container.insertAdjacentHTML('beforeend', html);
  itineraryItemCount++;
}

// Agregar item inicial al abrir modal de itinerario
document.getElementById('modalCreateItinerary')?.addEventListener('shown.bs.modal', () => {
  const c = document.getElementById('itinerary-items-container');
  if (!c) return;
  c.innerHTML = '';
  itineraryItemCount = 0;
  addItineraryItem();
});
</script>
