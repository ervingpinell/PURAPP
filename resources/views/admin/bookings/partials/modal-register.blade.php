{{-- resources/views/admin/bookings/partials/modal-register.blade.php --}}

@once
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endonce

<div class="modal fade" id="modalRegister" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <form id="createBookingForm"
          action="{{ route('admin.bookings.store') }}"
          method="POST"
          novalidate
          class="needs-validation">
      @csrf

      {{-- Bandera para reabrir modal al volver con errores --}}
      <input type="hidden" name="_modal" value="register">

      {{-- Asegura booking_date aunque no venga visible en el form --}}
      <input type="hidden" name="booking_date" value="{{ old('booking_date', now()->toDateString()) }}">

      <div class="modal-content">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title">
            <i class="fas fa-plus-circle me-2"></i>{{ __('m_bookings.bookings.ui.register_booking') }}
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">

          {{-- Errores generales --}}
          @if (session('error') && (session('openModal') === 'register' || old('_modal') === 'register'))
            <div class="alert alert-danger mb-3">
              {{ session('error') }}
            </div>
          @endif

          {{-- Lista de validaciones del backend --}}
          @if ($errors->any() && (session('openModal') === 'register' || old('_modal') === 'register'))
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $err)
                  <li>{{ $err }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          {{-- FORMULARIO PRINCIPAL (usa los mismos IDs que consumen tus scripts) --}}
          @include('admin.bookings.partials.form')
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-1"></i>{{ __('m_bookings.bookings.buttons.save') }}
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>{{ __('m_bookings.bookings.buttons.cancel') }}
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Reabrir modal si venimos de errores/validación --}}
@if (session('openModal') === 'register' || (old('_modal') === 'register' && $errors->any()) || session()->has('error'))
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const el = document.getElementById('modalRegister');
      if (!el) return;
      if (window.bootstrap?.Modal) {
        bootstrap.Modal.getOrCreateInstance(el).show();
      } else if (window.$ && $('#modalRegister').modal) {
        $('#modalRegister').modal('show');
      }
    });
  </script>
@endif

@push('css')
<style>
  .modal-xl .modal-body {
    max-height: 70vh;
    overflow-y: auto;
  }
</style>
@endpush
