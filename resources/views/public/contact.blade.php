@extends('layouts.app')

@section('meta_title'){{ branding('seo_contact_title_' . app()->getLocale(), __('adminlte::adminlte.meta.contact_title')) }}@endsection
@section('meta_description'){{ branding('seo_contact_description_' . app()->getLocale(), __('adminlte::adminlte.meta.contact_description')) }}@endsection

@push('styles')
@vite(['resources/css/contact.css'])
<style>
  /* Oculta el widget flotante (botón + panel) solo en esta página */
  .whatsapp-widget,
  .whatsapp-float-btn {
    display: none !important;
  }
</style>
@endpush

@section('content')
<section class="contact-section py-5 text-white">
  <div class="container">
    <div class="row gy-4 justify-content-center align-items-stretch flex-wrap">
      <div class="col-lg-7 col-md-12 d-flex">
        <div class="card shadow-sm border flex-fill w-100">
          <div class="card-header text-white">
            <h4 class="mb-0">{{ __('adminlte::adminlte.contact_us') }}</h4>
          </div>

          <div class="card-body">
            <form action="{{ localized_route('contact.send') }}" method="POST" id="contactForm">
              @csrf
              <input type="hidden" name="_t" value="{{ $timeToken }}">

              {{-- Honeypot --}}
              <div style="position:absolute; left:-9999px; top:-9999px;">
                <label for="website">Website</label>
                <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
              </div>

              <div class="mb-3">
                <label for="name" class="form-label">{{ __('adminlte::adminlte.name') }}</label>
                <input
                  type="text"
                  class="form-control form-control-lg @error('name') is-invalid @enderror"
                  name="name"
                  id="name"
                  required
                  value="{{ old('name') }}"
                  autocomplete="name"
                  placeholder="{{ __('adminlte::adminlte.contact_name_placeholder') }}">
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @else
                <div class="invalid-feedback">{{ __('adminlte::adminlte.field_required') }}</div>
                @enderror
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">{{ __('adminlte::adminlte.email') }}</label>
                <input
                  type="email"
                  class="form-control form-control-lg @error('email') is-invalid @enderror"
                  name="email"
                  id="email"
                  required
                  value="{{ old('email') }}"
                  autocomplete="email"
                  inputmode="email"
                  placeholder="{{ __('adminlte::adminlte.contact_email_placeholder') }}">
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @else
                <div class="invalid-feedback">{{ __('adminlte::adminlte.email_invalid') }}</div>
                @enderror
              </div>

              <div class="mb-3">
                <label for="subject" class="form-label">{{ __('adminlte::adminlte.subject') }}</label>
                <input
                  type="text"
                  class="form-control form-control-lg @error('subject') is-invalid @enderror"
                  name="subject"
                  id="subject"
                  required
                  value="{{ old('subject') }}"
                  placeholder="{{ __('adminlte::adminlte.contact_subject_placeholder') }}">
                @error('subject')
                <div class="invalid-feedback">{{ $message }}</div>
                @else
                <div class="invalid-feedback">{{ __('adminlte::adminlte.field_required') }}</div>
                @enderror
              </div>

              <div class="mb-3">
                <label for="message" class="form-label">{{ __('adminlte::adminlte.message') }}</label>
                <textarea
                  class="form-control form-control-lg @error('message') is-invalid @enderror"
                  name="message"
                  id="message"
                  rows="6"
                  required
                  minlength="5"
                  placeholder="{{ __('adminlte::adminlte.contact_message_placeholder') }}">{{ old('message') }}</textarea>
                @error('message')
                <div class="invalid-feedback">{{ $message }}</div>
                @else
                <div class="invalid-feedback">{{ __('adminlte::adminlte.field_required') }}</div>
                @enderror
              </div>

              {{-- Cloudflare Turnstile (solo si hay site_key configurada) --}}
              @if(config('services.turnstile.site_key'))
              <div class="mb-3">
                <div
                  class="cf-turnstile"
                  data-sitekey="{{ config('services.turnstile.site_key') }}"
                  data-theme="light"></div>
                @error('cf-turnstile-response')
                <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
              </div>
              @endif

              <button type="submit" class="btn btn-contact w-100">
                <i class="fas fa-paper-plane me-1"></i> {{ __('adminlte::adminlte.send_message') }}
              </button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-5 col-md-12 d-flex">
        <div class="card shadow-sm border bg-light flex-fill w-100">
          <div class="card-body">
            <h4 class="fw-bold mb-3">{{ __('adminlte::adminlte.contact_us') }}</h4>

            <p class="mb-2">
              <i class="fas fa-map-marker-alt me-2 text-success"></i>
              {{ config('company.address.city') }}, {{ config('company.address.state') }}, {{ config('company.address.country') }}
            </p>

            <p class="mb-2">
              <i class="fas fa-phone me-2 text-success"></i>
              {{ config('company.phone') }}
            </p>

            <p class="mb-3">
              <i class="fas fa-envelope me-2 text-success"></i>
              <a href="mailto:{{ config('company.email') }}">{{ config('company.email') }}</a>
            </p>

            <h5 class="mt-4">
              <i class="fas fa-clock me-2 text-success"></i>{{ __('adminlte::adminlte.business_hours') }}
            </h5>
            <span class="badge bg-success fs-6 text-wrap text-start lh-base">
              {{ __('adminlte::adminlte.business_schedule') }}
            </span>

            <div class="mt-3">
              @include('partials.ws-widget', [
              'variant' => 'inline',
              'buttonClass' => 'btn btn-outline-success',
              'phone' => config('company.phone_raw'),
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

{{-- Script de Cloudflare Turnstile, solo en esta vista y solo si está configurado --}}
@if(config('services.turnstile.site_key'))
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@endif

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');

    // Mostrar SweetAlert si hay éxito
    @if(session('success'))
    Swal.fire({
      icon: 'success',
      title: '{{ __('
      adminlte::adminlte.message_sent ') }}',
      html: `{!! session('success') !!}`,
      confirmButtonText: '{{ __('
      adminlte::adminlte.swal_ok ') }}'
    });
    @endif

    // Mostrar SweetAlert si hay errores de validación del servidor
    @if($errors -> any())
    Swal.fire({
      icon: 'error',
      title: '{{ __('
      adminlte::adminlte.validation_error ') }}',
      html: `
        <ul class="text-start">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      `,
      confirmButtonText: '{{ __('
      adminlte::adminlte.swal_ok ') }}'
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
