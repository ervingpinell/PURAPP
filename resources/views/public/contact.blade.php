@extends('layouts.app')
@vite(['resources/css/tour.css', 'resources/js/public.js'])

@section('title', __('adminlte::adminlte.contact_us'))

@section('content')
<section class="contact-section py-5 text-white">
  <div class="container">
    <div class="row gy-4 justify-content-center align-items-stretch flex-wrap">

      {{-- 📝 Formulario --}}
      <div class="col-lg-7 col-md-12 d-flex">
        @if(session('success'))
          <script>
            Swal.fire({
              icon: 'success',
              title: '{{ __('adminlte::adminlte.message_sent') }}',
              html: `{!! session('success') !!}`,
              confirmButtonText: 'OK'
            });
          </script>
        @endif

        @if ($errors->any())
          <div class="alert alert-danger w-100">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="card shadow-sm border flex-fill w-100">
          <div class="card-header bg-green-dark text-white">
            <h4 class="mb-0">{{ __('adminlte::adminlte.contact_us') }}</h4>
          </div>

          <div class="card-body">
            <form action="{{ route('contact.send') }}" method="POST">
              @csrf

              <div class="mb-3">
                <label for="name" class="form-label">{{ __('adminlte::adminlte.name') }}</label>
                <input type="text" class="form-control" name="name" id="name" required value="{{ old('name') }}">
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">{{ __('adminlte::adminlte.email') }}</label>
                <input type="email" class="form-control" name="email" id="email" required value="{{ old('email') }}">
              </div>

              <div class="mb-3">
                <label for="subject" class="form-label">{{ __('adminlte::adminlte.subject') }}</label>
                <input type="text" class="form-control" name="subject" id="subject" required value="{{ old('subject') }}">
              </div>

              <div class="mb-3">
                <label for="message" class="form-label">{{ __('adminlte::adminlte.message') }}</label>
                <textarea class="form-control" name="message" id="message" rows="5" required>{{ old('message') }}</textarea>
              </div>

              <button type="submit" class="btn btn-success bg-green-dark w-100">
                <i class="fas fa-paper-plane me-1"></i> {{ __('adminlte::adminlte.send_message') }}
              </button>
            </form>
          </div>
        </div>
      </div>

      {{-- 📍 Información de contacto + Mapa --}}
      <div class="col-lg-5 col-md-12 d-flex">
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

            {{-- 🕒 Horario --}}
            <h5 class="mt-4">
              <i class="fas fa-clock me-2 text-success"></i>{{ __('adminlte::adminlte.business_hours') }}
            </h5>
            <span class="badge bg-success fs-6">
              {{ __('adminlte::adminlte.business_schedule') }}
            </span>

            {{-- 🗺️ Mapa de Google --}}
            <div class="ratio ratio-4x3 mt-4">
              <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3923.569916995397!2d-84.6532029!3d10.455662299999998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8fa00c5bdfd9a475%3A0x7c12d295387f6352!2sAgencia%20de%20Viajes%20Green%20Vacation!5e0!3m2!1ses!2scr!4v1753803457057!5m2!1ses!2scr"
                width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

{{-- ✅ Modal de WhatsApp --}}
@include('partials.ws-widget')
@endsection
