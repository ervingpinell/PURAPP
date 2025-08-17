<!-- Modal: Crear Itinerario -->
<div class="modal fade" id="modalCrearItinerario" tabindex="-1" aria-labelledby="modalCrearItinerarioLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form action="{{ route('admin.tours.itinerary.store') }}"
          method="POST"
          class="modal-content form-create-itinerary">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="modalCrearItinerarioLabel">Crear nuevo itinerario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="name" class="form-label">Nombre del itinerario</label>
          <input type="text" name="name" id="name" class="form-control" required maxlength="255">
        </div>
        <div class="mb-3">
          <label for="description" class="form-label">Descripción (opcional)</label>
          <textarea name="description" id="description" class="form-control" maxlength="1000"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Crear</button>
      </div>
    </form>
  </div>
</div>
