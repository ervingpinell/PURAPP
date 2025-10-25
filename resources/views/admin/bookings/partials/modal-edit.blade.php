{{-- resources/views/admin/bookings/partials/modal-edit.blade.php --}}

@once
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endonce

@php
  /** @var \App\Models\Booking $booking */
  $detail = $booking->detail;
  $reopen = (session('showEditModal') == $booking->booking_id)
         || (old('_modal') === 'edit:'.$booking->booking_id && $errors->any());
@endphp

<div class="modal fade" id="modalEdit{{ $booking->booking_id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <form id="editBookingForm-{{ $booking->booking_id }}"
          action="{{ route('admin.bookings.update', $booking->booking_id) }}"
          method="POST"
          novalidate
          class="needs-validation js-edit-booking-form"
          data-booking-id="{{ $booking->booking_id }}">
      @csrf
      @method('PUT')
      <input type="hidden" name="_modal" value="edit:{{ $booking->booking_id }}">

      {{-- Asegura booking_date aunque no sea visible en el form --}}
      <input type="hidden" name="booking_date"
             value="{{ old('booking_date', optional($booking->booking_date)->format('Y-m-d') ?? now()->toDateString()) }}">

      {{-- Cliente bloqueado: lo enviamos escondido también --}}
      <input type="hidden" name="user_id" value="{{ $booking->user_id }}">

      <div class="modal-content">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title">
            <i class="fas fa-edit me-2"></i>
            {{ __('m_bookings.bookings.ui.edit_booking') }}
            <span class="text-muted">#{{ $booking->booking_id }}</span>
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          {{-- Errores del modal --}}
          @if ($reopen && (session('error') || $errors->any()))
            <div class="alert alert-danger mb-3">
              @if (session('error'))
                <div class="mb-2">{{ session('error') }}</div>
              @endif
              @if ($errors->any())
                <ul class="mb-0">
                  @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                  @endforeach
                </ul>
              @endif
            </div>
          @endif

          {{-- FORM DE EDICIÓN (precargado + cliente bloqueado) --}}
          @include('admin.bookings.partials.form-edit', [
            'booking' => $booking,
            'detail'  => $detail,
          ])
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-1"></i>{{ __('m_bookings.bookings.buttons.update') }}
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>{{ __('m_bookings.bookings.buttons.cancel') }}
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

@if ($reopen)
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const el = document.getElementById('modalEdit{{ $booking->booking_id }}');
      if (el) bootstrap.Modal.getOrCreateInstance(el).show();
    });
  </script>
@endif

@push('css')
<style>
  .modal-xl .modal-body { max-height: 70vh; overflow-y: auto; }
</style>
@endpush
