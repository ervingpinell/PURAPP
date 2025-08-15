{{-- resources/views/profile/edit.blade.php --}}
@extends('adminlte::auth.auth-page', ['authType' => 'login'])

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

  /* Centrar logo (como tu versión vieja) */
  .login-logo { text-align: center !important; }
  .login-logo img { margin: 0 auto; display: block; }
</style>
@endpush

@section('auth_body')
    {{-- Alert de éxito simple (si prefieres SweetAlert, avísame y lo cambio) --}}
    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    {{-- Bloque de errores del backend --}}
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

        {{-- Nombre Completo --}}
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
                value={{ old('email', auth()->user()->email) }}
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

        {{-- Teléfono: código de país + número nacional (guardados por separado) --}}
        <div class="mb-3">
            <div class="input-group">
                <select id="phone_cc" name="country_code"
                        class="form-select @error('country_code') is-invalid @enderror"
                        style="max-width: 140px;">
                    @include('partials.country-codes') {{-- ( +código ) al cerrar; País (+código) al abrir --}}
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

        {{-- Nueva contraseña (opcional) --}}
        <div class="input-group mb-3">
            <input type="password" name="password" id="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="{{ __('adminlte::validation.attributes.password') }} ({{ __('adminlte::adminlte.optional') ?? 'Opcional' }})"
                autocomplete="new-password">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span id="toggle-password" class="fas fa-eye {{ config('adminlte.classes_auth_icon', '') }}" style="cursor: pointer;"></span>
                </div>
            </div>

            @error('password')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        {{-- Confirmar contraseña --}}
        <div class="input-group mb-3">
            <input type="password" name="password_confirmation" id="password_confirmation"
                class="form-control @error('password_confirmation') is-invalid @enderror"
                placeholder="{{ __('adminlte::validation.attributes.password_confirmation') }}"
                autocomplete="new-password">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span id="toggle-password-confirmation" class="fas fa-eye {{ config('adminlte.classes_auth_icon', '') }}" style="cursor: pointer;"></span>
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
    {{-- Footer: botón volver a la izquierda + idioma a la derecha --}}
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
    // Mostrar/ocultar contraseña
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('toggle-password');
    const passwordConfirmationInput = document.getElementById('password_confirmation');
    const togglePasswordConfirmation = document.getElementById('toggle-password-confirmation');

    function toggleVisibility(input, toggleIcon) {
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            toggleIcon.classList.replace('fa-eye','fa-eye-slash');
        } else {
            input.type = 'password';
            toggleIcon.classList.replace('fa-eye-slash','fa-eye');
        }
    }

    if (togglePassword) togglePassword.addEventListener('click', () => toggleVisibility(passwordInput, togglePassword));
    if (togglePasswordConfirmation) togglePasswordConfirmation.addEventListener('click', () => toggleVisibility(passwordConfirmationInput, togglePasswordConfirmation));

    // Select de código: cerrado -> "(+código)"; abierto -> "País (+código)"
    const cc = document.getElementById('phone_cc');
    if (cc) {
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
        collapseLabels(); // init

        // Preselecciona el código guardado del usuario si existe
        const currentCode = @json(old('country_code', auth()->user()->country_code ?? null));
        if (currentCode) cc.value = currentCode;
    }
});
</script>
@endpush
