{{-- resources/views/admin/bookings/partials/edit-form.blade.php --}}
@csrf
@method('PUT')

<div class="mb-3">
  <label class="form-label">Estado</label>
  <select name="status" class="form-control">
    @foreach($statuses as $val => $label)
      <option value="{{ $val }}" {{ $booking->status === $val ? 'selected':'' }}>
        {{ $label }}
      </option>
    @endforeach
  </select>
</div>

<div class="mb-3">
  <label class="form-label">Adultos</label>
  <input type="number"
         name="adults_quantity"
         class="form-control"
         value="{{ $booking->detail->adults_quantity }}"
         min="1" required>
</div>

<div class="mb-3">
  <label class="form-label">Niños</label>
  <input type="number"
         name="kids_quantity"
         class="form-control"
         value="{{ $booking->detail->kids_quantity }}"
         min="0" max="2" required>
</div>

<button type="submit" class="btn btn-primary">Guardar cambios</button>
