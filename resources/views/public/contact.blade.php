@extends('layouts.app')

@section('title', __('adminlte::adminlte.contact_us'))

@push('styles')
  @vite(['resources/css/contact.css'])
  <style>
    /* Oculta el widget flotante (botón + panel) solo en esta página */
    .whatsapp-widget,
    .whatsapp-float-btn { display:none !important; }
  </style>
@endpush

@section('content')
<section class="contact-section py-5 text-white">
  <div class="container">
    <div class="row gy-4 justify-content-center align-items-stretch flex-wrap">
      <div class="col-lg-8 col-md-12 d-flex">
        <div class="card shadow-sm border flex-fill w-100">
          <div class="card-header text-white">
            <h4 class="mb-0">{{ __('adminlte::adminlte.contact_us') }}</h4>
          </div>

          <div class="card-body">
            <form action="{{ localized_route('contact.send') }}" method="POST" id="contactForm">
              @csrf

              {{-- Honeypot --}}
              <div style="position:absolute; left:-9999px; top:-9999px;">
                <label for="website">Website</label>
                <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
              </div>

              <div class="mb-3">
                <label for="name" class="form-label">{{ __('adminlte::adminlte.name') }}</label>
                <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror"
                  name="name" id="name" required value="{{ old('name') }}" autocomplete="name">
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @else
                  <div class="invalid-feedback">{{ __('adminlte::adminlte.field_required') }}</div>
                @enderror
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">{{ __('adminlte::adminlte.email') }}</label>
                <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                  name="email" id="email" required value="{{ old('email') }}" autocomplete="email" inputmode="email">
                @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
                @else
                  <div class="invalid-feedback">{{ __('adminlte::adminlte.email_invalid') }}</div>
                @enderror
              </div>

              <div class="mb-3">
                <label for="subject" class="form-label">{{ __('adminlte::adminlte.subject') }}</label>
                <input type="text" class="form-control form-control-lg @error('subject') is-invalid @enderror"
                  name="subject" id="subject" required value="{{ old('subject') }}">
                @error('subject')
                  <div class="invalid-feedback">{{ $message }}</div>
                @else
                  <div class="invalid-feedback">{{ __('adminlte::adminlte.field_required') }}</div>
                @enderror
              </div>

              <div class="mb-3">
                <label for="message" class="form-label">{{ __('adminlte::adminlte.message') }}</label>
                <textarea class="form-control form-control-lg @error('message') is-invalid @enderror"
                  name="message" id="message" rows="6" required>{{ old('message') }}</textarea>
                @error('message')
                  <div class="invalid-feedback">{{ $message }}</div>
                @else
                  <div class="invalid-feedback">{{ __('adminlte::adminlte.field_required') }}</div>
                @enderror
              </div>

              <button type="submit" class="btn btn-success bg-green-dark w-100">
                <i class="fas fa-paper-plane me-1"></i> {{ __('adminlte::adminlte.send_message') }}
              </button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-12 d-flex">
        <div class="card shadow-sm border bg-light flex-fill w-100">
          <div class="card-body">
            <h4 class="fw-bold mb-3">{{ __('adminlte::adminlte.contact_us') }}</h4>

            <p class="mb-2">
              <i class="fas fa-map-marker-alt me-2 text-success"></i>
              La Fortuna, San Carlos, Costa Rica
            </p>

            <p class="mb-2">
              <i class="fas fa-phone me-2 text-success"></i>
              (+506) 2479 1471
            </p>

            <p class="mb-3">
              <i class="fas fa-envelope me-2 text-success"></i>
              <a href="mailto:info@greenvacationscr.com">info@greenvacationscr.com</a>
            </p>

            <h5 class="mt-4">
              <i class="fas fa-clock me-2 text-success"></i>{{ __('adminlte::adminlte.business_hours') }}
            </h5>
            <span class="badge bg-success fs-6">
              {{ __('adminlte::adminlte.business_schedule') }}
            </span>

            <div class="mt-3">
              @include('partials.ws-widget', [
                  'variant'        => 'inline',
                  'buttonClass'    => 'btn btn-outline-success',
                  'phone'          => '50624791471',
              ])
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-12">
        <div class="card shadow-sm border bg-light">
          <div class="card-body p-2 p-sm-3">
            <div class="ratio ratio-16x9">
              <iframe src="{{ $mapSrc }}" style="border:0;" allowfullscreen loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('contactForm');

  // Mostrar SweetAlert si hay éxito
  @if(session('success'))
    Swal.fire({
      icon: 'success',
      title: '{{ __('adminlte::adminlte.message_sent') }}',
      html: `{!! session('success') !!}`,
      confirmButtonText: 'OK'
    });
  @endif

  // Mostrar SweetAlert si hay errores de validación del servidor
  @if ($errors->any())
    Swal.fire({
      icon: 'error',
      title: '{{ __('adminlte::adminlte.validation_error') }}',
      html: `
        <ul class="text-start">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      `,
      confirmButtonText: 'OK'
    });
  @endif

  // Validación HTML5 con Bootstrap styling
  form.addEventListener('submit', function(event) {
    if (!form.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
    }
    form.classList.add('was-validated');
  });
});
</script>
@endpush
