<!-- Modal asignar ítems -->
<div class="modal fade" id="modalAsignar{{ $itinerary->itinerary_id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('admin.tours.itinerary.assignItems', $itinerary->itinerary_id) }}"
          method="POST"
          class="form-assign-items"
          data-itinerary-id="{{ $itinerary->itinerary_id }}">
      @csrf
      <div class="modal-content">

        <div class="modal-header flex-wrap">
          <h5 class="modal-title w-100 fw-bold fs-4 mb-2">
            Asignar ítems a {{ $itinerary->name }}
          </h5>
          <span class="text-warning small ps-4">
            Arrastre y suelte los items para definir el orden.
          </span>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          @php
            $assigned    = $itinerary->items->keyBy('item_id');
            $unassigned  = $items->filter(fn($i) => !$assigned->has($i->item_id))->sortBy('title');
            $sortedItems = $assigned->concat($unassigned);
          @endphp

          <ul class="list-group sortable-items" id="sortable-{{ $itinerary->itinerary_id }}">
            @foreach ($sortedItems as $item)
              @continue(!$item->is_active)
              <li class="list-group-item d-flex justify-content-between align-items-center"
                  data-id="{{ $item->item_id }}">
                <div class="form-check">
                  <input type="checkbox"
                         class="form-check-input checkbox-assign"
                         value="{{ $item->item_id }}"
                         id="item-{{ $itinerary->itinerary_id }}-{{ $item->item_id }}"
                         {{ $assigned->has($item->item_id) ? 'checked' : '' }}>
                  <label class="form-check-label" for="item-{{ $itinerary->itinerary_id }}-{{ $item->item_id }}">
                    <strong>{{ $item->title }}</strong>
                  </label>
                </div>
                <i class="fas fa-arrows-alt handle text-muted"></i>
              </li>
            @endforeach
          </ul>

          {{-- dummy para evitar "no hay item_ids" si no seleccionan nada (lo quitamos en JS) --}}
          <input type="hidden" name="item_ids[dummy]" value="-1">
          <div id="ordered-inputs-{{ $itinerary->itinerary_id }}"></div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>

      </div>
    </form>
  </div>
</div>
