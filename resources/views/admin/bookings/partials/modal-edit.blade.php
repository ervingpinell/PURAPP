{{-- Fallback: if $meetingPoints doesn't come from controller, load it here --}}
@php
  /** @var \Illuminate\Support\Collection<int,\App\Models\MeetingPoint> $meetingPoints */
  $meetingPoints = $meetingPoints
    ?? \App\Models\MeetingPoint::orderByRaw('sort_order IS NULL, sort_order ASC')
        ->orderBy('name','asc')
        ->get();
@endphp

{{-- Edit Modal --}}
<div class="modal fade" id="modalEdit{{ $booking->booking_id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form
      id="editForm-{{ $booking->booking_id }}"
      class="js-edit-booking-form"
      action="{{ route('admin.bookings.update', $booking->booking_id) }}"
      method="POST"
      novalidate
      data-booking-id="{{ $booking->booking_id }}">
      @csrf
      @method('PUT')
      <input type="hidden" name="_modal" value="edit:{{ $booking->booking_id }}"><!-- 👈 To reopen this modal -->

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            Booking #{{ $booking->booking_id }} — {{ $booking->user->full_name ?? 'Client' }}
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          @php
            $showMyErrors = (session('showEditModal') == $booking->booking_id)
                            || (old('_modal') === 'edit:'.$booking->booking_id);
            $detail = $booking->detail;
          @endphp

          @if ($showMyErrors && $errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $err)
                  <li>{{ $err }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          {{-- ========== EXISTING MAIN FORM ========== --}}
          @include('admin.bookings.partials.edit-form', [
            'booking'  => $booking,
            'statuses' => [
              'pending'   => 'Pending',
              'confirmed' => 'Confirmed',
              'cancelled' => 'Cancelled',
            ],
          ])

          {{-- ========== MEETING POINT (simple) ========== --}}
          <hr class="my-3">
          <div class="mb-2">
            <label class="form-label"><i class="fas fa-map-marker-alt me-1"></i> Meeting Point</label>
            <select
              name="meeting_point_id"
              class="form-select @error('meeting_point_id') is-invalid @enderror">
              <option value="">— Select a meeting point —</option>
              @foreach ($meetingPoints as $mp)
                <option
                  value="{{ $mp->id }}"
                  @selected(old('meeting_point_id', $detail->meeting_point_id ?? null) == $mp->id)>
                  {{ $mp->name }}
                </option>
              @endforeach
            </select>
            @error('meeting_point_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">
              Only the <strong>name</strong> of the point is shown in the list.
            </div>
          </div>
          {{-- /MEETING POINT --}}
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Update</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Reopen this modal if appropriate --}}
@if (session('showEditModal') == $booking->booking_id || (old('_modal') === 'edit:'.$booking->booking_id && $errors->any()))
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const m = document.getElementById('modalEdit{{ $booking->booking_id }}');
      if (m) new bootstrap.Modal(m).show();
    });
  </script>
@endif

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('editForm-{{ $booking->booking_id }}');
  if (!form) return;

  const btnSubmit = form.querySelector('button[type="submit"]');

  // Prevent double submit + spinner
  form.addEventListener('submit', (e) => {
    if (form.dataset.submitted === 'true') { e.preventDefault(); return; }
    form.dataset.submitted = 'true';
    if (btnSubmit) {
      btnSubmit.disabled = true;
      btnSubmit.dataset.originalText = btnSubmit.innerHTML;
      btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Updating...';
    }
  });

  // Toggle "Other hotel" (scoped to modal)
  const hotelSel      = form.querySelector('select[name="hotel_id"]');
  const otherWrap     = form.querySelector('[data-role="other-hotel-wrapper"]');
  const isOtherHidden = form.querySelector('input[name="is_other_hotel"]');

  const toggleOtherHotel = () => {
    if (!hotelSel || !otherWrap || !isOtherHidden) return;
    const isOther = hotelSel.value === 'other';
    otherWrap.classList.toggle('d-none', !isOther);
    isOtherHidden.value = isOther ? 1 : 0;
    if (!isOther) {
      const otherInput = form.querySelector('input[name="other_hotel_name"]');
      if (otherInput) otherInput.value = '';
    }
  };
  toggleOtherHotel();
  hotelSel?.addEventListener('change', toggleOtherHotel);

  // Update schedules when tour changes
  const tourSel  = form.querySelector('select[name="tour_id"]');
  const schedSel = form.querySelector('select[name="schedule_id"]');

  tourSel?.addEventListener('change', () => {
    const opt  = tourSel.selectedOptions[0];
    const json = opt ? opt.getAttribute('data-schedules') : '[]';
    let list = [];
    try { list = JSON.parse(json || '[]'); } catch(e) { console.error(e); }

    schedSel.innerHTML = '<option value="">Select schedule</option>';
    list.forEach(s => {
      const o = document.createElement('option');
      o.value = s.schedule_id;
      o.textContent = `${s.start_time} — ${s.end_time}`;
      schedSel.appendChild(o);
    });
    schedSel.value = '';
  });
});
</script>
