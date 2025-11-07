{{-- Modal: Crear Categoría --}}
<div class="modal fade" id="modalCreateCategory" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('m_tours.tour.modal.create_category') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formCreateCategory">
          <div class="mb-3">
            <label>{{ __('m_tours.tour.modal.fields.name') }} *</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>{{ __('m_tours.tour.modal.fields.age_range') }}</label>
            <input type="text" name="age_range" class="form-control" placeholder="0-12 años">
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label>{{ __('m_tours.tour.modal.fields.min') }} *</label>
              <input type="number" name="min_quantity" class="form-control" value="0" required>
            </div>
            <div class="col-md-6 mb-3">
              <label>{{ __('m_tours.tour.modal.fields.max') }} *</label>
              <input type="number" name="max_quantity" class="form-control" value="12" required>
            </div>
          </div>
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
<div class="modal fade" id="modalCreateLanguage" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('m_tours.tour.modal.create_language') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formCreateLanguage">
          <div class="mb-3">
            <label>{{ __('m_tours.language.fields.name') }} *</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>{{ __('m_tours.language.fields.code') }} *</label>
            <input type="text" name="code" class="form-control" placeholder="es" maxlength="5" required>
            <small class="form-text text-muted">ISO 639-1 (ej: es, en, fr)</small>
          </div>
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
<div class="modal fade" id="modalCreateAmenity" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('m_tours.tour.modal.create_amenity') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formCreateAmenity">
          <div class="mb-3">
            <label>{{ __('m_tours.amenity.fields.name') }} *</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>{{ __('m_tours.amenity.fields.icon') }}</label>
            <input type="text" name="icon" class="form-control" value="fas fa-check" placeholder="fas fa-check">
            <small class="form-text text-muted">FontAwesome icon class</small>
          </div>
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
<div class="modal fade" id="modalCreateSchedule" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('m_tours.tour.modal.create_schedule') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formCreateSchedule">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label>{{ __('m_tours.schedule.fields.start_time') }} *</label>
              <input type="time" name="start_time" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label>{{ __('m_tours.schedule.fields.end_time') }} *</label>
              <input type="time" name="end_time" class="form-control" required>
            </div>
          </div>
          <div class="mb-3">
            <label>{{ __('m_tours.schedule.fields.label_optional') }}</label>
            <input type="text" name="label" class="form-control" placeholder="Matutino">
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
<script>
let itineraryItemCount = 0;

function addItineraryItem() {
  const container = document.getElementById('itinerary-items-container');
  const html = `
    <div class="card mb-2 itinerary-item-card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-10">
            <div class="mb-2">
              <input type="text" name="items[${itineraryItemCount}][title]"
                     class="form-control form-control-sm" placeholder="{{ __('m_tours.itinerary.fields.item_title') }}" required>
            </div>
            <div>
              <input type="text" name="items[${itineraryItemCount}][description]"
                     class="form-control form-control-sm" placeholder="{{ __('m_tours.itinerary.fields.item_description') }}">
            </div>
          </div>
          <div class="col-md-2 text-end">
            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.itinerary-item-card').remove()">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  `;
  container.insertAdjacentHTML('beforeend', html);
  itineraryItemCount++;
}

// Agregar item inicial al abrir modal
document.getElementById('modalCreateItinerary')?.addEventListener('shown.bs.modal', () => {
  document.getElementById('itinerary-items-container').innerHTML = '';
  itineraryItemCount = 0;
  addItineraryItem();
});
</script>
