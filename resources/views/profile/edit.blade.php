@extends('adminlte::auth.auth-page', ['authType' => 'profile'])

@php
    $updateUrl = route('profile.update');
    $homeUrl   = route('home');
@endphp

@section('title', __('adminlte::adminlte.edit_profile'))

@section('auth_header')
    <h3 class="text-center mb-0">
        <i class="fas fa-user-edit me-2"></i>
        {{ __('adminlte::adminlte.edit_profile') }}
    </h3>
@stop

@push('css')
<style>
  .form-group .invalid-feedback { display:block; }
  .input-group .input-group-text { min-width: 42px; justify-content: center; }
  .login-logo { text-align: center !important; }
  .login-logo img { margin: 0 auto; display: block; }
</style>
@endpush

@section('auth_body')
    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    {{-- Errors --}}
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

    <form action="{{ $updateUrl }}" method="POST" id="publicProfileForm" novalidate>
        @csrf

        {{-- Full name --}}
        <div class="input-group mb-3">
            <input type="text" name="full_name" id="full_name"
                class="form-control @error('full_name') is-invalid @enderror"
                value="{{ old('full_name', auth()->user()->full_name) }}"
                placeholder="{{ __('adminlte::validation.attributes.full_name') }}" required autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('full_name')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        {{-- Email --}}
        <div class="input-group mb-3">
            <input type="email" name="email" id="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', auth()->user()->email) }}"
                placeholder="{{ __('adminlte::validation.attributes.email') }}" required autocomplete="email">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('email')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        {{-- Phone --}}
        <div class="mb-3">
            <div class="input-group">
                <select id="phone_cc" name="country_code"
                        class="form-select @error('country_code') is-invalid @enderror"
                        style="max-width: 140px;">
                    @include('partials.country-codes')
                </select>

                <input type="tel" name="phone" id="phone"
                       class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone', auth()->user()->phone) }}"
                       placeholder="{{ __('adminlte::validation.attributes.phone') }}"
                       inputmode="tel" autocomplete="tel">

                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-phone {{ config('adminlte.classes_auth_icon', '') }}"></span>
                    </div>
                </div>
            </div>
            @error('country_code') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            @error('phone')        <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        {{-- New password (optional) --}}
        <div class="input-group mb-3">
            <input type="password" name="password" id="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="{{ __('adminlte::validation.attributes.password') }} ({{ __('adminlte::adminlte.optional') ?? 'Opcional' }})"
                autocomplete="new-password">

            <div class="input-group-append">
                <div class="input-group-text">
                    <a href="#" class="text-reset toggle-password" data-target="password" aria-label="{{ __('adminlte::adminlte.show_password') }}">
                      <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>

            @error('password')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        {{-- Confirm password --}}
        <div class="input-group mb-3">
            <input type="password" name="password_confirmation" id="password_confirmation"
                class="form-control @error('password_confirmation') is-invalid @enderror"
                placeholder="{{ __('adminlte::validation.attributes.password_confirmation') }}"
                autocomplete="new-password">

            <div class="input-group-append">
                <div class="input-group-text">
                    <a href="#" class="text-reset toggle-password" data-target="password_confirmation" aria-label="{{ __('adminlte::adminlte.show_password') }}">
                      <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>

            @error('password_confirmation')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <button type="submit" class="btn btn-block btn-info">
            <i class="fas fa-save"></i> {{ __('adminlte::adminlte.save') }}
        </button>
    </form>
@stop

@section('auth_footer')
  <div class="d-flex justify-content-between align-items-center">
    <a href="{{ $homeUrl }}" class="btn btn-outline-secondary">
      <i class="fas fa-arrow-left mr-1"></i> {{ __('adminlte::adminlte.back') }}
    </a>
    @include('partials.language-switcher')
  </div>
@stop

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
  // toggle password
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

  // country code labels
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
  cc.addEventListener('blur',  collapseLabels);
  collapseLabels();

  const currentCode = @json(old('country_code', auth()->user()->country_code ?? null));
  if (currentCode) cc.value = currentCode;
});
</script>
@endpush
