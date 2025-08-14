{{-- resources/views/vendor/adminlte/auth/register.blade.php --}}
@extends('adminlte::auth.auth-page', ['authType' => 'register'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@push('css')
<style>
    .form-group .invalid-feedback { display:block; }
    .input-group .input-group-text { min-width: 42px; justify-content: center; }
    #password-requirements { font-size: .875rem; list-style: none; padding-left: 1rem; }
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

    {{-- Alert de errores del backend (usa adminlte::validation) --}}
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

    {{-- Evitar prompts nativos del navegador en inglés --}}
    <form id="registerForm" action="{{ $registerUrl }}" method="POST" novalidate>
        @csrf

        {{-- Full name --}}
        <div class="form-group mb-3">
            <div class="input-group">
                <input
                    type="text"
                    name="full_name"
                    class="form-control @error('full_name') is-invalid @enderror"
                    value="{{ old('full_name') }}"
                    placeholder="{{ __('adminlte::validation.attributes.full_name') }}"
                    autocomplete="name"
                >
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-user {{ config('adminlte.classes_auth_icon', '') }}"></span>
                    </div>
                </div>
            </div>
            @error('full_name')
                <div class="invalid-feedback srv" data-for="full_name">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="form-group mb-3">
            <div class="input-group">
                <input
                    type="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                    placeholder="{{ __('adminlte::validation.attributes.email') }}"
                    autocomplete="email"
                >
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                    </div>
                </div>
            </div>
            @error('email')
                <div class="invalid-feedback srv" data-for="email">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Phone (opcional) --}}
        <div class="form-group mb-3">
            <div class="input-group">
                <input
                    type="text"
                    name="phone"
                    class="form-control @error('phone') is-invalid @enderror"
                    value="{{ old('phone') }}"
                    placeholder="{{ __('adminlte::validation.attributes.phone') }}"
                    autocomplete="tel"
                >
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-phone {{ config('adminlte.classes_auth_icon', '') }}"></span>
                    </div>
                </div>
            </div>
            @error('phone')
                <div class="invalid-feedback srv" data-for="phone">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group mb-1">
            <div class="input-group">
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="{{ __('adminlte::validation.attributes.password') }}"
                    autocomplete="new-password"
                >
                <div class="input-group-append">
                    <div class="input-group-text">
                        <a href="#" class="text-reset toggle-password" data-target="password"
                           aria-label="{{ __('adminlte::adminlte.show_password') ?? 'Mostrar contraseña' }}">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
            </div>
            @error('password')
                <div class="invalid-feedback srv" data-for="password">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Requisitos de contraseña (guía visual) --}}
        <ul id="password-requirements" class="mb-3">
            <li id="req-length"  class="text-muted">{{ __('adminlte::validation.password_requirements.length') }}</li>
            <li id="req-special" class="text-muted">{{ __('adminlte::validation.password_requirements.special') }}</li>
            <li id="req-number"  class="text-muted">{{ __('adminlte::validation.password_requirements.number') }}</li>
        </ul>

        {{-- Confirm password --}}
        <div class="form-group mb-3">
            <div class="input-group">
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-control @error('password_confirmation') is-invalid @enderror"
                    placeholder="{{ __('adminlte::validation.attributes.password_confirmation') }}"
                    autocomplete="new-password"
                >
                <div class="input-group-append">
                    <div class="input-group-text">
                        <a href="#" class="text-reset toggle-password" data-target="password_confirmation"
                           aria-label="{{ __('adminlte::adminlte.show_password') ?? 'Mostrar contraseña' }}">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
            </div>
            @error('password_confirmation')
                <div class="invalid-feedback srv" data-for="password_confirmation">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn w-100 text-nowrap {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
            <span class="fas fa-user-plus me-2"></span>
            {{ __('adminlte::adminlte.register') }}
        </button>
    </form>
@endsection

@section('auth_footer')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <p class="mb-0">
                <a href="{{ $loginUrl }}">
                    {{ __('adminlte::adminlte.i_already_have_a_membership') }}
                </a>
            </p>
        </div>
        <div>@include('partials.language-switcher')</div>
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
    // Mostrar/ocultar contraseña (UX)
    document.querySelectorAll('.toggle-password').forEach(toggle => {
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
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

    // Requisitos de contraseña (solo guía visual)
    const passwordInput = document.getElementById('password');
    const reqLength  = document.getElementById('req-length');
    const reqSpecial = document.getElementById('req-special');
    const reqNumber  = document.getElementById('req-number');

    function markReq(el, ok) {
        if (!el) return;
        el.classList.toggle('text-success', ok);
        el.classList.toggle('text-muted', !ok);
    }

    if (passwordInput) {
        passwordInput.addEventListener('input', function () {
            const v = passwordInput.value || '';
            markReq(reqLength,  v.length >= 8);
            markReq(reqSpecial, /[.:!@#$%^&*()_+\-]/.test(v));
            markReq(reqNumber,  /\d/.test(v));
        });
    }

    // UX: al teclear/cambiar, ocultar mensajes del servidor del campo y el alert general
    const form = document.getElementById('registerForm');
    const serverAlert = document.getElementById('server-errors');

    form.querySelectorAll('input').forEach(input => {
        ['input','change'].forEach(evt => input.addEventListener(evt, () => {
            if (serverAlert) serverAlert.classList.add('d-none');
            const group = input.closest('.form-group');
            group?.querySelectorAll(`.invalid-feedback.srv[data-for="${input.name}"]`).forEach(el => el.classList.add('d-none'));
            input.classList.remove('is-invalid');
        }));
    });

    // IMPORTANTE: no bloqueamos el submit; la validación es SOLO del backend
});
</script>
@endpush
