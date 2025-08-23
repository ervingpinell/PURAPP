@extends('layouts.app')

@section('title', __('adminlte::adminlte.contact_us'))

@section('content')
<section class="contact-section py-5 text-white">
  <div class="container">

    {{-- ====== FILA PRINCIPAL: Form + Info ====== --}}
    <div class="row gy-4 justify-content-center align-items-stretch flex-wrap">

      {{-- 📝 Formulario --}}
      <div class="col-lg-8 col-md-12 d-flex">
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
            <form action="{{ route('contact.send') }}" method="POST" novalidate>
              @csrf

              {{-- Honeypot anti-spam: oculto con CSS --}}
              <div style="position:absolute; left:-9999px; top:-9999px;">
                <label for="website">Website</label>
                <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
              </div>

              <div class="mb-3">
                <label for="name" class="form-label">{{ __('adminlte::adminlte.name') }}</label>
                <input
                  type="text"
                  class="form-control form-control-lg @error('name') is-invalid @enderror"
                  name="name" id="name" required value="{{ old('name') }}"
                  autocomplete="name">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">{{ __('adminlte::adminlte.email') }}</label>
                <input
                  type="email"
                  class="form-control form-control-lg @error('email') is-invalid @enderror"
                  name="email" id="email" required value="{{ old('email') }}"
                  autocomplete="email" inputmode="email">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="mb-3">
                <label for="subject" class="form-label">{{ __('adminlte::adminlte.subject') }}</label>
                <input
                  type="text"
                  class="form-control form-control-lg @error('subject') is-invalid @enderror"
                  name="subject" id="subject" required value="{{ old('subject') }}">
                @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="mb-3">
                <label for="message" class="form-label">{{ __('adminlte::adminlte.message') }}</label>
                <textarea
                  class="form-control form-control-lg @error('message') is-invalid @enderror"
                  name="message" id="message" rows="6" required>{{ old('message') }}</textarea>
                @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <button type="submit" class="btn btn-success bg-green-dark w-100">
                <i class="fas fa-paper-plane me-1"></i> {{ __('adminlte::adminlte.send_message') }}
              </button>
            </form>
          </div>
        </div>
      </div>

      {{-- 📍 Información de contacto (sin mapa aquí) --}}
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

            {{-- 🕒 Horario --}}
            <h5 class="mt-4">
              <i class="fas fa-clock me-2 text-success"></i>{{ __('adminlte::adminlte.business_hours') }}
            </h5>
            <span class="badge bg-success fs-6">
              {{ __('adminlte::adminlte.business_schedule') }}
            </span>

            {{-- 💬 WhatsApp inline (dentro de la card) --}}
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

    </div> {{-- /row --}}

    {{-- ====== FILA SECUNDARIA: Mapa a pantalla completa debajo ====== --}}
    @php
      // Mapeo de locales de la app -> códigos válidos del parámetro "hl" de Google Maps Embed
      $locale       = app()->getLocale();
      $hlMap = [
        'es'    => 'es',
        'es-CR' => 'es',
        'en'    => 'en',
        'en-US' => 'en',
        'en-GB' => 'en',
        'fr'    => 'fr',
        'fr-FR' => 'fr',
        'pt'    => 'pt',
        'pt-PT' => 'pt',
        'pt-BR' => 'pt-BR',
        'de'    => 'de',
        'de-DE' => 'de',
        'it'    => 'it',
        'nl'    => 'nl',
        'ru'    => 'ru',
        'ja'    => 'ja',
        'zh'    => 'zh-CN',  // chino simplificado
        'zh-CN' => 'zh-CN',
        'zh-TW' => 'zh-TW',  // chino tradicional
      ];
      // Por defecto, si no hay match, usa inglés
      $mapLang = $hlMap[$locale] ?? 'en';
    @endphp

    <div class="row mt-4">
      <div class="col-12">
        <div class="card shadow-sm border bg-light">
          <div class="card-body p-2 p-sm-3">
<div class="ratio ratio-16x9">
  <iframe
    src="{{ $mapSrc }}"
    style="border:0;" allowfullscreen loading="lazy"
    referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>


          </div>
        </div>
      </div>
    </div>

  </div>{{-- /container --}}
</section>
@endsection
