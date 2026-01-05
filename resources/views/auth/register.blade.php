@extends('adminlte::auth.auth-page', ['authType' => 'register'])

@section('dashboard_url', '/')

@section('adminlte_css_pre')
<link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@push('css')
<style>
  .form-group .invalid-feedback {
    display: block;
  }

  .input-group .input-group-text {
    min-width: 42px;
    justify-content: center;
  }

  #password-requirements {
    font-size: .875rem;
    list-style: none;
    padding-left: 1rem;
    margin-top: .5rem;
  }

  #password-requirements li {
    transition: color .15s ease-in-out;
  }

  /* Widen the register box - AUMENTADO */
  .register-box {
    width: 480px !important;
    max-width: 95vw !important;
  }

  @media (max-width: 576px) {
    .register-box {
      width: 95% !important;
      margin-top: 10px;
    }
  }

  /* Language switcher dropdown fix */
  .language-switcher {
    position: relative;
    z-index: 1050;
  }

  .language-switcher .dropdown-menu {
    position: absolute;
    z-index: 1060;
  }

  .language-switcher .dropdown-menu.show {
    display: block;
  }

  /* Mejorar simetría de campos en fila */
  .row-field-group {
    display: flex;
    gap: 0.75rem;
  }

  .row-field-group>div {
    flex: 1;
  }

  /* Asegurar que los inputs tengan el mismo ancho */
  .form-control,
  .form-select {
    width: 100%;
  }
</style>
@endpush

@php
$loginUrl = View::getSection('login_url') ?? config('adminlte.login_url', 'login');
$registerUrl = View::getSection('register_url') ?? config('adminlte.register_url', 'register');

if (config('adminlte.use_route_url', false)) {
$loginUrl = $loginUrl ? route($loginUrl) : '';
$registerUrl = $registerUrl ? route($registerUrl) : '';
} else {
$loginUrl = $loginUrl ? url($loginUrl) : '';
$registerUrl = $registerUrl ? url($registerUrl) : '';
}
@endphp

@section('title', __('adminlte::adminlte.register'))
@section('auth_header', __('adminlte::adminlte.register_message'))

@section('auth_body')

@if ($errors->any())
<div id="server-errors" class="alert alert-danger">
  <h5 class="mb-2">
    <i class="icon fas fa-exclamation-triangle"></i>
    {{ __('adminlte::validation.validation_error_title') }}
  </h5>
  <ul class="mb-0 ps-3">
    @foreach ($errors->all() as $error)
    <li class="srv-error">{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

<form id="registerForm" action="{{ $registerUrl }}" method="POST" novalidate>
  @csrf

  {{-- Name Fields Row --}}
  <div class="row row-field-group mb-3">
    <div class="col-6">
      <div class="form-group mb-0">
        <div class="input-group">
          <input type="text" name="first_name"
            class="form-control @error('first_name') is-invalid @enderror"
            value="{{ old('first_name') }}"
            placeholder="{{ __('adminlte::validation.attributes.first_name') }}"
            autocomplete="given-name" required>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-user"></span></div>
          </div>
        </div>
        @error('first_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
      </div>
    </div>
    <div class="col-6">
      <div class="form-group mb-0">
        <div class="input-group">
          <input type="text" name="last_name"
            class="form-control @error('last_name') is-invalid @enderror"
            value="{{ old('last_name') }}"
            placeholder="{{ __('adminlte::validation.attributes.last_name') }}"
            autocomplete="family-name" required>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-user"></span></div>
          </div>
        </div>
        @error('last_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
      </div>
    </div>
  </div>

  {{-- Email Field --}}
  <div class="form-group mb-3">
    <div class="input-group">
      <input type="email" name="email"
        class="form-control @error('email') is-invalid @enderror"
        value="{{ old('email') }}"
        placeholder="{{ __('adminlte::adminlte.email') }}"
        autocomplete="email" required>
      <div class="input-group-append">
        <div class="input-group-text"><span class="fas fa-envelope"></span></div>
      </div>
    </div>
    @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
  </div>

  {{-- Address Field --}}
  <div class="form-group mb-3">
    <div class="input-group">
      <input type="text" name="address"
        class="form-control @error('address') is-invalid @enderror"
        value="{{ old('address') }}"
        placeholder="{{ __('adminlte::adminlte.address') }}"
        autocomplete="street-address" required>
      <div class="input-group-append">
        <div class="input-group-text"><span class="fas fa-map-marker-alt"></span></div>
      </div>
    </div>
    @error('address') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
  </div>

  {{-- City and State Row --}}
  <div class="row row-field-group mb-3">
    <div class="col-6">
      <div class="form-group mb-0">
        <input type="text" name="city"
          class="form-control @error('city') is-invalid @enderror"
          value="{{ old('city') }}"
          placeholder="{{ __('adminlte::adminlte.city') }}"
          autocomplete="address-level2" required>
        @error('city') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
      </div>
    </div>
    <div class="col-6">
      <div class="form-group mb-0">
        <input type="text" name="state"
          class="form-control @error('state') is-invalid @enderror"
          value="{{ old('state') }}"
          placeholder="{{ __('adminlte::adminlte.state') }}"
          autocomplete="address-level1" required>
        @error('state') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
      </div>
    </div>
  </div>

  {{-- ZIP and Country Row --}}
  <div class="row row-field-group mb-3">
    <div class="col-6">
      <div class="form-group mb-0">
        <input type="text" name="zip"
          class="form-control @error('zip') is-invalid @enderror"
          value="{{ old('zip') }}"
          placeholder="{{ __('adminlte::adminlte.zip') }}"
          autocomplete="postal-code" required>
        @error('zip') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
      </div>
    </div>
    <div class="col-6">
      <div class="form-group mb-0">
        <select name="country" id="register_country"
          class="form-control @error('country') is-invalid @enderror" required>
          @include('partials.country-codes', [
          'selected' => old('country', 'CR'),
          'showNames' => true,
          'valueIsIso' => true
          ])
        </select>
        @error('country') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
      </div>
    </div>
  </div>

  {{-- Phone Field --}}
  <div class="form-group mb-3">
    <div class="input-group">
      <select id="phone_cc" name="country_code"
        class="form-select @error('country_code') is-invalid @enderror"
        style="max-width: 140px;">
        @include('partials.country-codes')
      </select>
      <input type="tel" id="phone" name="phone"
        class="form-control @error('phone') is-invalid @enderror"
        value="{{ old('phone') }}"
        placeholder="{{ __('adminlte::validation.attributes.phone') }}"
        inputmode="tel" autocomplete="tel" required>
      <div class="input-group-append">
        <div class="input-group-text"><span class="fas fa-phone"></span></div>
      </div>
    </div>
    @error('country_code') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
  </div>

  {{-- Password Field --}}
  <div class="form-group mb-1">
    <div class="input-group">
      <input type="password" id="password" name="password"
        class="form-control @error('password') is-invalid @enderror"
        placeholder="{{ __('adminlte::validation.attributes.password') }}"
        autocomplete="new-password" required>
      <div class="input-group-append">
        <div class="input-group-text">
          <a href="#" class="text-reset toggle-password" data-target="password"
            aria-label="{{ __('adminlte::adminlte.show_password') }}">
            <i class="fas fa-eye"></i>
          </a>
        </div>
      </div>
    </div>
    @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
  </div>

  {{-- Password Requirements --}}
  <ul id="password-requirements" class="mb-3">
    <li id="req-length" class="text-muted">{{ __('adminlte::validation.password_requirements.length') }}</li>
    <li id="req-special" class="text-muted">{{ __('adminlte::validation.password_requirements.special') }}</li>
    <li id="req-number" class="text-muted">{{ __('adminlte::validation.password_requirements.number') }}</li>
  </ul>

  {{-- Password Confirmation --}}
  <div class="form-group mb-3">
    <div class="input-group">
      <input type="password" id="password_confirmation" name="password_confirmation"
        class="form-control @error('password_confirmation') is-invalid @enderror"
        placeholder="{{ __('adminlte::validation.attributes.password_confirmation') }}"
        autocomplete="new-password" required>
      <div class="input-group-append">
        <div class="input-group-text">
          <a href="#" class="text-reset toggle-password" data-target="password_confirmation"
            aria-label="{{ __('adminlte::adminlte.show_password') }}">
            <i class="fas fa-eye"></i>
          </a>
        </div>
      </div>
    </div>
    @error('password_confirmation') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
  </div>

  {{-- Submit Button --}}
  <button type="submit" class="btn btn-primary w-100">
    <span class="fas fa-user-plus me-2"></span> {{ __('adminlte::adminlte.register') }}
  </button>
</form>
@endsection

@section('auth_footer')
<div class="d-flex justify-content-between align-items-center">
  <a href="{{ $loginUrl }}" class="text-success">
    {{ __('adminlte::adminlte.i_already_have_a_membership') }}
  </a>
  @include('partials.language-switcher')
</div>
<div class="mt-3 text-center">
  <a href="{{ url('/login') }}" class="btn btn-outline-secondary">
    <i class="fas fa-arrow-left mr-1"></i> {{ __('adminlte::adminlte.back') }}
  </a>
</div>
@endsection

@push('js')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
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

    // Password validation hints
    const pwd = document.getElementById('password');
    const reqLen = document.getElementById('req-length');
    const reqSp = document.getElementById('req-special');
    const reqNum = document.getElementById('req-number');

    function mark(el, ok) {
      if (!el) return;
      el.classList.toggle('text-success', ok);
      el.classList.toggle('text-muted', !ok);
    }

    function checkPasswordHints() {
      const v = (pwd.value || '');
      mark(reqLen, v.length >= 8);
      mark(reqSp, /[.\u00A1!@#$%^&*()_+\-]/.test(v));
      mark(reqNum, /\d/.test(v));
    }

    if (pwd) {
      pwd.addEventListener('input', checkPasswordHints);
      checkPasswordHints();
    }

    // Phone country code labels - expand on focus, collapse on blur
    const cc = document.getElementById('phone_cc');
    if (cc) {
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
    }
  });
</script>
@endpush