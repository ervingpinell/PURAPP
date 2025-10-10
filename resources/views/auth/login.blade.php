@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('dashboard_url', '/')
@section('title', __('Login'))

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@php
    $loginUrl     = View::getSection('login_url') ?? config('adminlte.login_url', 'login');
    $registerUrl  = View::getSection('register_url') ?? config('adminlte.register_url', 'register');
    $passResetUrl = route('password.request');

    if (config('adminlte.use_route_url', false)) {
        $loginUrl     = $loginUrl     ? route($loginUrl)     : '';
        $registerUrl  = $registerUrl  ? route($registerUrl)  : '';
    } else {
        $loginUrl     = $loginUrl     ? url($loginUrl)       : '';
        $registerUrl  = $registerUrl  ? url($registerUrl)    : '';
    }

    $t = function ($key, $fallback) { $val = __($key); return $val !== $key ? $val : $fallback; };

    $lblRememberEmail  = $t('auth.remember_me', 'Recordar mi correo en este equipo');
    $turnstileSiteKey  = config('services.turnstile.site_key', '');

    $captchaFlag = (bool) session('login.captcha');
    $appDebug    = (bool) config('app.debug');
    $dbg         = session('login.debug') ?? [];
@endphp

@section('auth_header', __('adminlte::auth.login_message'))

@section('auth_body')

    {{-- === PANEL DE DEBUG (APP_DEBUG=true) ===
    @if($appDebug)
        <div class="alert alert-secondary" style="font-size:.9rem">
            <strong>DEBUG CAPTCHA</strong><br>
            captchaFlag: <code>{{ $captchaFlag ? 'TRUE' : 'FALSE' }}</code><br>
            siteKey presente: <code>{{ !empty($turnstileSiteKey) ? 'TRUE' : 'FALSE' }}</code>
            @if(!empty($turnstileSiteKey))
                &nbsp;|&nbsp; prefix: <code>{{ substr($turnstileSiteKey,0,8) }}…</code>
            @endif
            <br>
            route(login): <code>{{ $loginUrl }}</code> &nbsp;|&nbsp; env(APP_ENV): <code>{{ app()->environment() }}</code>
            @if(!empty($dbg))
                <hr class="my-2">
                <div>counters:</div>
                <pre class="mb-0">{{ json_encode($dbg, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
            @endif
        </div>
    @endif --}}



    {{-- Mensajes --}}
    @if (session('status'))
      <div class="alert alert-success"><i class="fas fa-check-circle me-1"></i>{{ session('status') }}</div>
    @endif
    @if(session('success')) <div class="alert alert-success alert-dismissible">{{ session('success') }}</div> @endif
    @if(session('error'))   <div class="alert alert-danger alert-dismissible">{{ session('error') }}</div>   @endif

    {{-- Errores --}}
    @if($errors->has('email'))
        <div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-1"></i>{{ $errors->first('email') }}</div>
    @endif
    @if($errors->has('password'))
        <div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-1"></i>{{ $errors->first('password') }}</div>
    @endif

    <form action="{{ $loginUrl }}" method="post" autocomplete="on" novalidate>
        @csrf

        {{-- Email --}}
        <div class="input-group mb-3">
            <input
                type="email"
                name="email"
                id="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', request()->cookie('remembered_email')) }}"
                placeholder="{{ $t('adminlte::adminlte.email', 'Correo electrónico') }}"
                required
                autocomplete="username"
                autocapitalize="none"
                autocorrect="off"
                spellcheck="false"
                pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$"
                title="{{ $t('validation.email', 'Ingresa un correo válido') }}"
            >
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>
            @error('email')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        {{-- Password --}}
        <div class="input-group mb-3">
            <input
                type="password"
                name="password"
                id="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="{{ $t('adminlte::adminlte.password', 'Contraseña') }}"
                required
                autocomplete="current-password"
            >
            <div class="input-group-append">
                <div class="input-group-text">
                    <a href="#" class="text-reset toggle-password" data-target="password" aria-label="Mostrar u ocultar contraseña">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
            @error('password')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        {{-- Recordar correo --}}
        <div class="row mb-3">
            <div class="col-12">
                <div class="icheck-primary">
                    <input type="checkbox" name="remember_email" id="remember_email"
                           {{ request()->cookie('remembered_email') ? 'checked' : '' }}>
                    <label for="remember_email">{{ $lblRememberEmail }}</label>
                </div>
            </div>
        </div>

        {{-- CAPTCHA (Turnstile) si hay flag y key --}}
        @if ($captchaFlag)
            @if (!empty($turnstileSiteKey))
                <div class="mb-3">
                    <div class="cf-turnstile" data-sitekey="{{ $turnstileSiteKey }}"></div>
                    @error('captcha')
                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            @else
                <div class="alert alert-warning">
                    Turnstile activo pero falta <code>TURNSTILE_SITE_KEY</code>. Configura la key y ejecuta <code>php artisan config:clear</code>.
                </div>
            @endif
        @endif

        {{-- Olvidé mi contraseña --}}
        <p class="mb-1">
            <a href="{{ $passResetUrl }}">{{ $t('adminlte::auth.i_forgot_my_password', '¿Olvidaste tu contraseña?') }}</a>
        </p>

        {{-- Botón --}}
        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn w-100 text-nowrap {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                    <span class="fas fa-sign-in-alt me-2"></span>
                    {{ $t('adminlte::auth.sign_in', 'Ingresar') }}
                </button>
            </div>
        </div>
    </form>
@stop

@section('auth_footer')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            @if($registerUrl)
                <p class="mb-0">
                    <a href="{{ $registerUrl }}" class="btn btn-success">
                        <i class="fas fa-user-plus me-1"></i> {{ $t('adminlte::auth.register', 'Crear cuenta') }}
                    </a>
                </p>
            @endif
        </div>
        <div>@include('partials.language-switcher')</div>
    </div>

    <div class="mt-3 text-center">
        <a href="{{ url('/') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> {{ $t('adminlte::adminlte.back', 'Volver') }}
        </a>
    </div>
@stop

@section('adminlte_js')
    @if ($captchaFlag && !empty($turnstileSiteKey))
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle password
        document.querySelectorAll('.toggle-password').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const input    = document.getElementById(this.dataset.target);
                if (!input) return;
                const icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    if (icon) icon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    input.type = 'password';
                    if (icon) icon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            });
        });

        // Mensaje de validación bonito para email
        const email = document.getElementById('email');
        if (email) {
            const msg = "{{ $t('validation.email', 'Ingresa un correo válido') }}";
            email.addEventListener('invalid', function () {
                if (email.validity.valueMissing) {
                    email.setCustomValidity("{{ $t('validation.required', 'Este campo es obligatorio') }}");
                } else if (email.validity.typeMismatch || email.validity.patternMismatch) {
                    email.setCustomValidity(msg);
                }
            });
            email.addEventListener('input', function () { email.setCustomValidity(''); });
        }
    });
    </script>
@stop
