<!-- Modal editar itinerario -->
<div class="modal fade" id="modalEditar{{ $itinerary->itinerary_id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('admin.tours.itinerary.update', $itinerary->itinerary_id) }}"
          method="POST"
          class="form-edit-itinerary">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ __('m_tours.itinerary.ui.edit') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('m_tours.itinerary.ui.close') }}"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="it-name-{{ $itinerary->itinerary_id }}" class="form-label">
              {{ __('m_tours.itinerary.fields.name') }}
            </label>
            <input type="text"
                   name="name"
                   id="it-name-{{ $itinerary->itinerary_id }}"
                   class="form-control"
                   value="{{ $itinerary->name }}"
                   required maxlength="255">
          </div>
          <div class="mb-3">
            <label for="it-desc-{{ $itinerary->itinerary_id }}" class="form-label">
              {{ __('m_tours.itinerary.fields.description') }}
            </label>
            <textarea name="description"
                      id="it-desc-{{ $itinerary->itinerary_id }}"
                      class="form-control"
                      rows="3"
                      maxlength="1000">{{ $itinerary->description }}</textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
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
