@foreach($tours as $tour)
<div class="modal fade"
     id="modalCart{{ $tour->tour_id }}"
     tabindex="-1"
     aria-hidden="true"
     data-max="{{ $tour->max_capacity }}">  {{-- expone max_capacity --}}
  <div class="modal-dialog">
    <form method="POST"
          action="{{ route('admin.cart.store') }}"
          class="modal-content">
      @csrf
      <input type="hidden" name="tour_id" value="{{ $tour->tour_id }}">
      <input type="hidden" name="adult_price" value="{{ $tour->adult_price }}">
      <input type="hidden" name="kid_price"   value="{{ $tour->kid_price }}">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Agregar al carrito: {{ $tour->name }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        {{-- Fecha --}}
        <div class="mb-3">
          <label>Fecha del tour</label>
          <input type="date"
                 name="tour_date"
                 class="form-control"
                 required>
        </div>

        {{-- Idioma --}}
        <div class="mb-3">
          <label>Idioma</label>
          <select name="tour_language_id"
                  class="form-control"
                  required>
            <option value="">Seleccione</option>
            @foreach($tour->languages as $lang)
              <option value="{{ $lang->tour_language_id }}">{{ $lang->name }}</option>
            @endforeach
          </select>
        </div>

        {{-- Hotel --}}
        <div class="mb-3">
          <label>Hotel</label>
          <select name="hotel_id"
                  id="hotel_select_{{ $tour->tour_id }}"
                  class="form-control"
                  required>
            <option value="">Seleccione un hotel</option>
            @foreach($hotels as $hotel)
              <option value="{{ $hotel->hotel_id }}">{{ $hotel->name }}</option>
            @endforeach
            <option value="other">Otro (ingresar nombre)</option>
          </select>
        </div>

        {{-- Otro hotel --}}
        <div class="mb-3 d-none"
             id="other_hotel_container_{{ $tour->tour_id }}">
          <label>Nombre de hotel</label>
          <input type="text"
                 name="other_hotel_name"
                 class="form-control"
                 placeholder="Escriba el nombre del hotel">
        </div>

        {{-- Cantidades --}}
        <div class="mb-3">
          <label>Cantidad de adultos</label>
          <input type="number"
                 name="adults_quantity"
                 class="form-control"
                 min="1"
                 value="1"
                 required>
        </div>
        <div class="mb-3">
          <label>Cantidad de niños</label>
          <input type="number"
                 name="kids_quantity"
                 class="form-control"
                 min="0" max="2"
                 value="0">
        </div>

        {{-- oculto para indicar otro hotel --}}
        <input type="hidden"
               name="is_other_hotel"
               id="is_other_hotel_{{ $tour->tour_id }}"
               value="0">
      </div>

      <div class="modal-footer">
        <button type="submit"
                class="btn btn-success w-100">
          <i class="fas fa-cart-plus"></i> Agregar al carrito
        </button>
      </div>
    </form>
  </div>
</div>
@endforeach
@push('js')
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.modal[data-max]').forEach(modal => {
    const form   = modal.querySelector('form');
    const maxCap = parseInt(modal.dataset.max, 10);

    form.addEventListener('submit', async function(e) {
      e.preventDefault();

      const tourIdEl = form.querySelector('[name="tour_id"]');
      const dateEl   = form.querySelector('[name="tour_date"]');
      const adultsEl = form.querySelector('[name="adults_quantity"]');
      const kidsEl   = form.querySelector('[name="kids_quantity"]');

      const tourDate  = dateEl.value;
      const adults    = parseInt(adultsEl.value, 10) || 0;
      const kids      = parseInt(kidsEl.value, 10)   || 0;
      const requested = adults + kids;

      if (!tourDate) {
        return Swal.fire({
          icon: 'warning',
          title: 'Selecciona la fecha del tour'
        });
      }

      // Consulta al backend
      let reserved = 0;
      try {
        // 1) Hacemos await y guardamos en resp
        const resp = await fetch(
          `/admin/reservas/reserved?tour_id=${tourIdEl.value}&tour_date=${tourDate}`,
          { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
        );
        // 2) Extraemos JSON
        const data = await resp.json();
        reserved = parseInt(data.reserved, 10) || 0;
      } catch (_) {
        return Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'No se pudo validar el cupo. Intenta más tarde.'
        });
      }

      // Validación
      if (reserved + requested > maxCap) {
        const available = Math.max(0, maxCap - reserved - requested);
        return Swal.fire({
          icon: 'error',
          title: 'Cupo Excedido',
          text: `Para este tour el ${tourDate} quedarían ${available} plazas disponibles.`
        });
      }

      // Si todo ok, enviamos el formulario
      this.submit();
    });
  });
});
</script>
@endpush
