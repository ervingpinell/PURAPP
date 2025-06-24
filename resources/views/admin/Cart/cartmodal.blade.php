@foreach($tours as $tour)
<div class="modal fade" id="modalCart{{ $tour->tour_id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('admin.cart.store') }}" class="modal-content">
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
          <input type="date" name="tour_date" class="form-control" required>
        </div>

        {{-- Idioma --}}
        <div class="mb-3">
          <label>Idioma</label>
          <select name="tour_language_id" class="form-control" required>
            <option value="">Seleccione</option>
            @foreach($tour->languages as $lang)
              <option value="{{ $lang->tour_language_id }}">{{ $lang->name }}</option>
            @endforeach
          </select>
        </div>

        {{-- **HOTEL** --}}
        <div class="mb-3">
          <label>Hotel</label>
          <select name="hotel_id"
                  id="hotel_select_{{ $tour->tour_id }}"
                  class="form-control" required>
            <option value="">Seleccione un hotel</option>
            @foreach($hotels as $hotel)
              <option value="{{ $hotel->hotel_id }}">{{ $hotel->name }}</option>
            @endforeach
            <option value="other">Otro (ingresar nombre)</option>
          </select>
        </div>

        {{-- **OTRO HOTEL** (oculto inicialmente) --}}
        <div class="mb-3 d-none" id="other_hotel_container_{{ $tour->tour_id }}">
          <label>Nombre de hotel</label>
          <input type="text"
                 name="other_hotel_name"
                 class="form-control"
                 placeholder="Escriba el nombre del hotel">
        </div>

        {{-- Cantidades --}}
        <div class="mb-3">
          <label>Cantidad de adultos</label>
          <input type="number" name="adults_quantity"
                 class="form-control" min="1" value="1" required>
        </div>
        <div class="mb-3">
          <label>Cantidad de niños</label>
          <input type="number" name="kids_quantity"
                 class="form-control" min="0" max="2" value="0">
        </div>

        {{-- hidden para indicar otro hotel --}}
        <input type="hidden"
               name="is_other_hotel"
               id="is_other_hotel_{{ $tour->tour_id }}"
               value="0">
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-success w-100">
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
  @foreach($tours as $tour)
    (function(){
      const sel   = document.getElementById('hotel_select_{{ $tour->tour_id }}');
      const cont  = document.getElementById('other_hotel_container_{{ $tour->tour_id }}');
      const hidden= document.getElementById('is_other_hotel_{{ $tour->tour_id }}');

      sel.addEventListener('change', () => {
        if (sel.value === 'other') {
          cont.classList.remove('d-none');
          hidden.value = 1;
        } else {
          cont.classList.add('d-none');
          cont.querySelector('input').value = '';
          hidden.value = 0;
        }
      });
    })();
  @endforeach
});
</script>
@endpush
