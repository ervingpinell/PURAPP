@foreach($tours as $tour)
<div class="modal fade" id="modalCart{{ $tour->tour_id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.cart.store') }}" class="modal-content">
            @csrf
            <input type="hidden" name="tour_id" value="{{ $tour->tour_id }}">
            <input type="hidden" name="adult_price" value="{{ $tour->adult_price }}">
            <input type="hidden" name="kid_price" value="{{ $tour->kid_price }}">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Agregar al carrito: {{ $tour->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label>Fecha del tour</label>
                    <input type="date" name="tour_date" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Idioma</label>
                    <select name="tour_language_id" class="form-control" required>
                        <option value="">Seleccione</option>
                        @foreach($tour->languages as $lang)
                            <option value="{{ $lang->tour_language_id }}">{{ $lang->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>Cantidad de adultos</label>
                    <input type="number" name="adults_quantity" class="form-control" min="1" value="1" required>
                </div>

                <div class="mb-3">
                    <label>Cantidad de niños</label>
                    <input type="number" name="kids_quantity" class="form-control" min="0" value="0">
                </div>
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