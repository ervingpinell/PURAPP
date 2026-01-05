{{-- resources/views/admin/profile/profile.blade.php --}}
@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit_profile'))

@push('css')
<style>
  /* ===== Logo arriba ===== */
  .profile-brand {
    display: flex;
    justify-content: center;
    margin: 10px 0 22px;
    /* espacio entre logo y tarjeta */
  }

  /* El enlace solo abarca la imagen, no toda la fila */
  .profile-brand .brand-link-inline {
    display: inline-block;
    padding: 0;
    margin: 0;
    line-height: 0;
    /* evita área extra vertical */
    border: 0;
    background: transparent;
  }

  .profile-brand .brand-link-inline:focus {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
    border-radius: .375rem;
  }

  .profile-brand .brand-img {
    height: 86px;
    /* ajusta si quieres más/menos alto */
    width: auto;
    display: block;
  }

  /* ===== Inputs y detalles ===== */
  .input-group .input-group-text {
    min-width: 42px;
    justify-content: center;
  }

  code.d-block {
    font-size: .95rem;
  }

  /* Mejorar simetría de campos en fila */
  .row-field-group {
    display: flex;
    gap: 0.75rem;
  }

  .row-field-group>div {
    flex: 1;
  }
</style>
@endpush

@section('content_header')
<h1 class="text-center">
  <i class="fas fa-user-edit"></i>
  {{ __('adminlte::adminlte.edit_profile_of', ['name' => $user->full_name]) }}
</h1>
@stop

@section('content')
@php
$statusMap = [
'two-factor-authentication-enabled' => 'auth.two_factor.enabled',
'two-factor-authentication-confirmed' => 'auth.two_factor.confirmed',
'two-factor-authentication-disabled' => 'auth.two_factor.disabled',
'recovery-codes-generated' => 'auth.two_factor.recovery_codes_generated',
];

// Logo e inicio PÚBLICO (usa route('home') si existe; fallback a '/')
$logoUrl = asset(config('adminlte.logo_img', 'images/logo.png'));
$homeUrl = \Illuminate\Support\Facades\Route::has('home')
? route('home')
: url('/');
@endphp

<div class="d-flex justify-content-center">
  <div class="col-md-8 col-lg-7">

    {{-- LOGO (linkea al HOME PÚBLICO) --}}
    <div class="profile-brand">
      <a href="{{ $homeUrl }}" class="brand-link-inline" aria-label="{{ config('app.name') }} home">
        <img src="{{ $logoUrl }}" alt="{{ config('app.name') }} logo" class="brand-img">
      </a>
    </div>

    {{-- Flashes (Fortify + propios) --}}
    @if (session('status'))
    <div class="alert alert-success">{{ __($statusMap[session('status')] ?? session('status')) }}</div>
    @endif
    @if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if (session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <div class="card card-primary shadow">
      <div class="card-header text-center">
        <h3 class="card-title w-100">
          <i class="fas fa-user-cog"></i> {{ __('adminlte::adminlte.profile_information') }}
        </h3>
      </div>

      {{-- Form principal (datos del perfil) --}}
      <form action="{{ route('admin.profile.update') }}" method="POST" id="adminProfileForm" novalidate>
        @csrf
        <div class="card-body">

          {{-- Name Fields Row --}}
          <div class="row row-field-group mb-3">
            <div class="col-6">
              <div class="form-group mb-0">
                <label class="form-label"><i class="fas fa-user"></i> {{ __('adminlte::validation.attributes.first_name') }}</label>
                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                  value="{{ old('first_name', $firstName) }}" autocomplete="given-name" required>
                @error('first_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
              </div>
            </div>
            <div class="col-6">
              <div class="form-group mb-0">
                <label class="form-label"><i class="fas fa-user"></i> {{ __('adminlte::validation.attributes.last_name') }}</label>
                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                  value="{{ old('last_name', $lastName) }}" autocomplete="family-name" required>
                @error('last_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
              </div>
            </div>
          </div>

          {{-- Email --}}
          <div class="form-group mb-3">
            <label class="form-label"><i class="fas fa-envelope"></i> {{ __('adminlte::validation.attributes.email') }}</label>
            <div class="input-group">
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', $user->email) }}" autocomplete="email" required>
              <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-envelope"></span></div>
              </div>
            </div>
            @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
          </div>

          {{-- Teléfono --}}
          <div class="form-group mb-3">
            <label class="form-label"><i class="fas fa-phone"></i> {{ __('adminlte::validation.attributes.phone') }}</label>
            <div class="input-group">
              <select id="phone_cc" name="country_code" class="form-control @error('country_code') is-invalid @enderror" style="max-width: 140px;">
                @include('partials.country-codes')
              </select>
              <input type="tel" id="phone" name="phone"
                class="form-control @error('phone') is-invalid @enderror"
                value="{{ old('phone', $user->phone) }}"
                placeholder="{{ __('adminlte::validation.attributes.phone') }}"
                inputmode="tel" autocomplete="tel">
              <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-phone"></span></div>
              </div>
            </div>
            @error('country_code') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
          </div>

          {{-- Address --}}
          <div class="form-group mb-3">
            <label class="form-label"><i class="fas fa-map-marker-alt"></i> {{ __('adminlte::adminlte.address') }}</label>
            <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
              value="{{ old('address', $user->address) }}" autocomplete="street-address" required>
            @error('address') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
          </div>

          {{-- City and State Row --}}
          <div class="row row-field-group mb-3">
            <div class="col-6">
              <div class="form-group mb-0">
                <label class="form-label"><i class="fas fa-city"></i> {{ __('adminlte::adminlte.city') }}</label>
                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                  value="{{ old('city', $user->city) }}" autocomplete="address-level2" required>
                @error('city') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
              </div>
            </div>
            <div class="col-6">
              <div class="form-group mb-0">
                <label class="form-label"><i class="fas fa-map"></i> {{ __('adminlte::adminlte.state') }}</label>
                <input type="text" name="state" class="form-control @error('state') is-invalid @enderror"
                  value="{{ old('state', $user->state) }}" autocomplete="address-level1" required>
                @error('state') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
              </div>
            </div>
          </div>

          {{-- ZIP and Country Row --}}
          <div class="row row-field-group mb-3">
            <div class="col-6">
              <div class="form-group mb-0">
                <label class="form-label"><i class="fas fa-mail-bulk"></i> {{ __('adminlte::adminlte.zip') }}</label>
                <input type="text" name="zip" class="form-control @error('zip') is-invalid @enderror"
                  value="{{ old('zip', $user->zip) }}" autocomplete="postal-code" required>
                @error('zip') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
              </div>
            </div>
            <div class="col-6">
              <div class="form-group mb-0">
                <label class="form-label"><i class="fas fa-flag"></i> {{ __('adminlte::adminlte.country') }}</label>
                <select name="country" id="country" class="form-control @error('country') is-invalid @enderror" required>
                  @include('partials.country-codes', [
                  'selected' => old('country', $user->country),
                  'valueIsIso' => true,
                  'showNames' => true
                  ])
                </select>
                @error('country') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
              </div>
            </div>
          </div>

          {{-- Password opcional --}}
          <div class="form-group mb-2">
            <label class="form-label"><i class="fas fa-lock"></i> {{ __('adminlte::validation.attributes.password') }}</label>
            <div class="input-group">
              <input type="password" id="password" name="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="{{ __('adminlte::validation.attributes.password') }}" autocomplete="new-password"
                aria-describedby="password-reqs-admin">
              <div class="input-group-append">
                <div class="input-group-text">
                  <a href="#" class="text-reset toggle-password" data-target="password" aria-label="{{ __('adminlte::adminlte.show_password') }}">
                    <i class="fas fa-eye"></i>
                  </a>
                </div>
              </div>
            </div>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- Requisitos de contraseña (guía visual) --}}
          <ul id="password-reqs-admin" class="mb-3" style="font-size:.9rem; list-style:none; padding-left:1rem;">
            <li id="req-length-admin" class="text-muted">{{ __('adminlte::validation.password_requirements.length') }}</li>
            <li id="req-special-admin" class="text-muted">{{ __('adminlte::validation.password_requirements.special') }}</li>
            <li id="req-number-admin" class="text-muted">{{ __('adminlte::validation.password_requirements.number') }}</li>
          </ul>

          {{-- Confirmación --}}
          <div class="form-group mb-1">
            <label class="form-label"><i class="fas fa-lock"></i> {{ __('adminlte::validation.attributes.password_confirmation') }}</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
              class="form-control @error('password_confirmation') is-invalid @enderror"
              placeholder="{{ __('adminlte::validation.attributes.password_confirmation') }}" autocomplete="new-password">
            @error('password_confirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="card-footer text-center">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ __('adminlte::adminlte.save_changes') }}
          </button>
        </div>
      </form>

      {{-- Bloque 2FA (separado del form principal) --}}
      <div class="card-body border-top">
        <h5 class="mb-3"><i class="fas fa-shield-alt me-1"></i> Autenticación en dos pasos (2FA)</h5>

        @if (empty($has2FA))
        {{-- Activar 2FA --}}
        <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
          @csrf
          <button type="submit" class="btn btn-outline-primary">
            <i class="fas fa-shield-alt me-1"></i> Activar 2FA
          </button>
        </form>
        @else
        @if (empty($is2FAConfirmed))
        <div class="alert alert-info">
          Escanea el QR y confirma tu código de 6 dígitos para finalizar la activación.
        </div>

        @if(!empty($qrSvg))
        <div class="border rounded p-3 bg-white mb-3">
          {!! $qrSvg !!}
        </div>
        @endif

        {{-- Confirmar TOTP --}}
        <form method="POST" action="{{ url('/user/confirmed-two-factor-authentication') }}" class="mb-3">
          @csrf
          <div class="input-group mb-2" style="max-width: 320px;">
            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
              placeholder="{{ __('auth.two_factor.code') }}" inputmode="numeric" autocomplete="one-time-code" autofocus>
            <div class="input-group-append">
              <button class="btn btn-success" type="submit">{{ __('auth.two_factor.confirm') }}</button>
            </div>
          </div>
          @error('code') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </form>
        @else
        {{-- Ya confirmado: mostramos QR (opcional) y códigos --}}
        @if(!empty($qrSvg))
        <div class="border rounded p-3 bg-white mb-3">
          {!! $qrSvg !!}
        </div>
        @endif

        <h6 class="mb-2">Códigos de recuperación</h6>
        @if (!empty($recoveryCodes) && count($recoveryCodes))
        <div class="row row-cols-2 row-cols-md-3 g-2">
          @foreach ($recoveryCodes as $code)
          <div class="col">
            <code class="d-block p-2 bg-light border rounded text-center">{{ $code }}</code>
          </div>
          @endforeach
        </div>
        @else
        <p class="text-muted mb-1">No hay códigos listados. Puedes regenerarlos.</p>
        @endif

        <form method="POST" action="{{ url('/user/two-factor-recovery-codes') }}" class="mt-3">
          @csrf
          <button class="btn btn-sm btn-secondary">
            <i class="fas fa-redo me-1"></i> Regenerar códigos de recuperación
          </button>
        </form>
        @endif

        {{-- Desactivar 2FA --}}
        <form method="POST" action="{{ url('/user/two-factor-authentication') }}" class="mt-3">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-outline-danger">
            <i class="fas fa-times me-1"></i> Desactivar 2FA
          </button>
        </form>
        @endif
      </div>

    </div>
  </div>
</div>
@stop

@push('js')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Toggle password
    document.querySelectorAll('.toggle-password').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        const input = document.getElementById(this.dataset.target);
        const icon = this.querySelector('i');
        if (!input) return;
        if (input.type === 'password') {
          input.type = 'text';
          icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
          input.type = 'password';
          icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
      });
    });

    // Requisitos de contraseña (hints)
    const pwd = document.getElementById('password');
    const reqLen = document.getElementById('req-length-admin');
    const reqSpec = document.getElementById('req-special-admin');
    const reqNum = document.getElementById('req-number-admin');

    function mark(el, ok) {
      if (!el) return;
      el.classList.toggle('text-success', ok);
      el.classList.toggle('text-muted', !ok);
    }
    if (pwd) {
      pwd.addEventListener('input', function() {
        const v = pwd.value || '';
        mark(reqLen, v.length >= 8);
        mark(reqSpec, /[.\u00A1!@#$%^&*()_+\-]/.test(v)); // incluye "¡"
        mark(reqNum, /\d/.test(v));
      });
    }

    // Phone country code labels
    const cc = document.getElementById('phone_cc');

    // Phone country code labels - expand on focus, collapse on blur
    function expandLabels() {
      Array.from(cc.options).forEach(opt => {
        const name = opt.dataset.name || '';
        const code = opt.dataset.code || opt.value;
        opt.textContent = `${name} (${code})`;
      });
    }

    function collapseLabels() {
      Array.from(cc.options).forEach(opt => {
        const code = opt.dataset.code || opt.value;
        opt.textContent = `(${code})`;
      });
    }

    cc.addEventListener('focus', expandLabels);
    cc.addEventListener('blur', collapseLabels);
    collapseLabels(); // Start collapsed

    const wantedCc = @json(old('country_code', $user - > country_code));
    if (wantedCc) {
      const found = Array.from(cc.options).find(o => o.value === wantedCc);
      if (found) cc.value = wantedCc;
    }
  });
</script>
@endpush