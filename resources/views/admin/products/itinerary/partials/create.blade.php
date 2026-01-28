<!-- Modal: Crear Itinerario -->
<div class="modal fade" id="modalCrearItinerario" tabindex="-1" aria-labelledby="modalCrearItinerarioLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form action="{{ route('admin.products.itinerary.store') }}"
          method="POST"
          class="modal-content form-create-itinerary">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="modalCrearItinerarioLabel">
          {{ __('m_tours.itinerary.ui.create_title') }}
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('m_tours.itinerary.ui.close') }}"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="new-itinerary-name" class="form-label">
            {{ __('m_tours.itinerary.fields.name') }}
          </label>
          <input type="text"
                 name="name"
                 id="new-itinerary-name"
                 class="form-control"
                 required maxlength="255">
        </div>
        <div class="mb-3">
          <label for="new-itinerary-description" class="form-label">
            {{ __('m_tours.itinerary.fields.description_optional') }}
          </label>
          <textarea name="description"
                    id="new-itinerary-description"
                    class="form-control"
                    maxlength="1000"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          {{ __('m_tours.itinerary.ui.cancel') }}
        </button>
        <button type="submit" class="btn btn-primary">
          {{ __('m_tours.itinerary.ui.create_button') }}
        </button>
      </div>
    </form>
  </div>
</div>
