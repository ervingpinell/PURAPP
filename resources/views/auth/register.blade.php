@extends('adminlte::auth.auth-page', ['authType' => 'register'])

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

@section('auth_header', __('adminlte::adminlte.register_message'))

@section('auth_body')
    <form action="{{ $registerUrl }}" method="post">
        @csrf

        {{-- Name field --}}
        <div class="input-group mb-3">
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name') }}" placeholder="{{ __('adminlte::adminlte.full_name') }}" autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Email field --}}
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}" placeholder="example@gmail.com">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password field --}}
        <div class="input-group mb-1">
            <input type="password" name="password" id="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="{{ __('adminlte::adminlte.password') }}">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span id="toggle-password" class="fas fa-eye {{ config('adminlte.classes_auth_icon', '') }}" style="cursor: pointer;"></span>
                </div>
            </div>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password requirements note --}}
<small class="d-block mb-3">
    <ul id="password-requirements" class="list-unstyled mb-0 small">
        <li id="req-length" class="text-danger">{{ __('adminlte::adminlte.password_requirements.length') }}</li>
        <li id="req-special" class="text-danger">{{ __('adminlte::adminlte.password_requirements.special') }}</li>
        <li id="req-number" class="text-danger">{{ __('adminlte::adminlte.password_requirements.number') }}</li>
    </ul>
</small>

        {{-- Confirm password field --}}
        <div class="input-group mb-3">
            <input type="password" name="password_confirmation" id="password_confirmation"
                class="form-control @error('password_confirmation') is-invalid @enderror"
                placeholder="{{ __('adminlte::adminlte.retype_password') }}">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span id="toggle-password-confirm" class="fas fa-eye {{ config('adminlte.classes_auth_icon', '') }}" style="cursor: pointer;"></span>
                </div>
            </div>

            @error('password_confirmation')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Register button --}}
        <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
            <span class="fas fa-user-plus"></span>
            {{ __('adminlte::adminlte.register') }}
        </button>
    </form>
@stop

@section('auth_footer')
    <p class="my-0">
        <a href="{{ $loginUrl }}">
            {{ __('adminlte::adminlte.i_already_have_a_membership') }}
        </a>
    </p>
@stop

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('toggle-password');
        const confirmInput = document.getElementById('password_confirmation');
        const toggleConfirm = document.getElementById('toggle-password-confirm');

        togglePassword.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            togglePassword.classList.toggle('fa-eye');
            togglePassword.classList.toggle('fa-eye-slash');
        });

        toggleConfirm.addEventListener('click', () => {
            const isPassword = confirmInput.type === 'password';
            confirmInput.type = isPassword ? 'text' : 'password';
            toggleConfirm.classList.toggle('fa-eye');
            toggleConfirm.classList.toggle('fa-eye-slash');
        });

        // Interacción en tiempo real con los requisitos
        passwordInput.addEventListener('input', () => {
            const val = passwordInput.value;

            const reqLength = document.getElementById('req-length');
            const reqSpecial = document.getElementById('req-special');
            const reqNumber = document.getElementById('req-number');

            // 8 caracteres
            reqLength.classList.toggle('text-success', val.length >= 8);
            reqLength.classList.toggle('text-danger', val.length < 8);

            // Caracter especial
            const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(val);
            reqSpecial.classList.toggle('text-success', hasSpecial);
            reqSpecial.classList.toggle('text-danger', !hasSpecial);

            // Número
            const hasNumber = /\d/.test(val);
            reqNumber.classList.toggle('text-success', hasNumber);
            reqNumber.classList.toggle('text-danger', !hasNumber);
        });
    });
</script>
@endpush
