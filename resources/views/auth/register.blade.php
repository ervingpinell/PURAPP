@extends('adminlte::auth.auth-page', ['authType' => 'register'])

@section('dashboard_url', '/')

@section('adminlte_css_pre')
  <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@push('css')
<style>
  .form-group .invalid-feedback { display:block; }
  .input-group .input-group-text { min-width: 42px; justify-content: center; }
  #password-requirements { font-size:.875rem; list-style:none; padding-left:1rem; margin-top:.5rem; }
  #password-requirements li { transition: color .15s ease-in-out; }
</style>
@endpush

@php
  $loginUrl    = View::getSection('login_url')    ?? config('adminlte.login_url', 'login');
  $registerUrl = View::getSection('register_url') ?? config('adminlte.register_url', 'register');

  if (config('adminlte.use_route_url', false)) {
      $loginUrl    = $loginUrl    ? route($loginUrl)    : '';
      $registerUrl = $registerUrl ? route($registerUrl) : '';
  } else {
      $loginUrl    = $loginUrl    ? url($loginUrl)    : '';
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

    <div class="form-group mb-3">
      <div class="input-group">
        <input type="text" name="full_name"
               class="form-control @error('full_name') is-invalid @enderror"
               value="{{ old('full_name') }}"
               placeholder="{{ __('adminlte::validation.attributes.full_name') }}" autocomplete="name">
        <div class="input-group-append">
          <div class="input-group-text"><span class="fas fa-user"></span></div>
        </div>
      </div>
      @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="form-group mb-3">
      <div class="input-group">
        <input type="email" name="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email') }}"
               placeholder="{{ __('adminlte::validation.attributes.email') }}" autocomplete="email">
        <div class="input-group-append">
          <div class="input-group-text"><span class="fas fa-envelope"></span></div>
        </div>
      </div>
      @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="form-group mb-3">
      <label class="form-label">{{ __('adminlte::validation.attributes.phone') }}</label>
      <div class="input-group">
        <select id="phone_cc" name="country_code" class="form-select @error('country_code') is-invalid @enderror" style="max-width: 140px;">
          @include('partials.country-codes')
        </select>
        <input type="tel" id="phone" name="phone"
               class="form-control @error('phone') is-invalid @enderror"
               value="{{ old('phone') }}"
               placeholder="{{ __('adminlte::validation.attributes.phone') }}"
               inputmode="tel" autocomplete="tel">
        <div class="input-group-append">
          <div class="input-group-text"><span class="fas fa-phone"></span></div>
        </div>
      </div>
      @error('country_code') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
      @error('phone')        <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <div class="form-group mb-1">
      <div class="input-group">
        <input type="password" id="password" name="password"
               class="form-control @error('password') is-invalid @enderror"
               placeholder="{{ __('adminlte::validation.attributes.password') }}" autocomplete="new-password">
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

    <ul id="password-requirements" class="mb-3">
      <li id="req-length"  class="text-muted">{{ __('adminlte::validation.password_requirements.length') }}</li>
      <li id="req-special" class="text-muted">{{ __('adminlte::validation.password_requirements.special') }}</li>
      <li id="req-number"  class="text-muted">{{ __('adminlte::validation.password_requirements.number') }}</li>
    </ul>

    <div class="form-group mb-3">
      <div class="input-group">
        <input type="password" id="password_confirmation" name="password_confirmation"
               class="form-control @error('password_confirmation') is-invalid @enderror"
               placeholder="{{ __('adminlte::validation.attributes.password_confirmation') }}" autocomplete="new-password">
        <div class="input-group-append">
          <div class="input-group-text">
            <a href="#" class="text-reset toggle-password" data-target="password_confirmation" aria-label="{{ __('adminlte::adminlte.show_password') }}">
              <i class="fas fa-eye"></i>
            </a>
          </div>
        </div>
      </div>
      @error('password_confirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <button type="submit" class="btn btn-primary w-100">
      <span class="fas fa-user-plus me-2"></span> {{ __('adminlte::adminlte.register') }}
    </button>
  </form>
@endsection

@section('auth_footer')
  <div class="d-flex justify-content-between align-items-center">
    <a href="{{ $loginUrl }}" class="text-success">{{ __('adminlte::adminlte.i_already_have_a_membership') }}</a>
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
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', function(e){
      e.preventDefault();
      const input = document.getElementById(this.dataset.target);
      const icon  = this.querySelector('i');
      if (!input) return;
      if (input.type === 'password') { input.type = 'text'; icon.classList.replace('fa-eye','fa-eye-slash'); }
      else { input.type = 'password'; icon.classList.replace('fa-eye-slash','fa-eye'); }
    });
  });

  const cc = document.getElementById('phone_cc');
  function expandLabels(){
    Array.from(cc.options).forEach(opt => {
      const name = opt.dataset.name || '';
      const code = opt.dataset.code || opt.value;
      opt.textContent = `${name} (${code})`;
    });
  }
  function collapseLabels(){
    Array.from(cc.options).forEach(opt => {
      const code = opt.dataset.code || opt.value;
      opt.textContent = `(${code})`;
    });
  }
  cc.addEventListener('focus', expandLabels);
  cc.addEventListener('blur', collapseLabels);
  collapseLabels();

  const pwd = document.getElementById('password');
  const reqLen = document.getElementById('req-length');
  const reqSp  = document.getElementById('req-special');
  const reqNum = document.getElementById('req-number');

  function mark(el, ok){
    if (!el) return;
    el.classList.toggle('text-success', ok);
    el.classList.toggle('text-muted', !ok);
  }

  function checkPasswordHints(){
    const v = (pwd.value || '');
    mark(reqLen, v.length >= 8);
    // incluye "¡" (U+00A1) en el set de especiales
    mark(reqSp,  /[.\u00A1!@#$%^&*()_+\-]/.test(v));
    mark(reqNum, /\d/.test(v));
  }

  if (pwd) {
    pwd.addEventListener('input', checkPasswordHints);
    checkPasswordHints();
  }
});
</script>
@endpush
