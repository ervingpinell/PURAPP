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

@section('title', __('adminlte::adminlte.register'))
@section('auth_header', __('adminlte::adminlte.register_message'))

@section('auth_body')
    <form action="{{ $registerUrl }}" method="POST">
        @csrf

        {{-- Full name --}}
        <div class="input-group mb-3">
            <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                value="{{ old('full_name') }}" placeholder="{{ __('adminlte::adminlte.full_name') }}" autofocus required>
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
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" required>
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
        <div class="input-group mb-3">
            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                value="{{ old('phone') }}" placeholder="{{ __('adminlte::adminlte.phone') }}" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-phone {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>
            @error('phone')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        {{-- Password --}}
        <div class="input-group mb-1">
            <input type="password" id="password" name="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="{{ __('adminlte::adminlte.password') }}" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <a href="#" class="text-reset toggle-password" data-target="password">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
            @error('password')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        {{-- Password requirements --}}
        <ul id="password-requirements" class="mb-3 pl-3" style="list-style: none; padding-left: 1rem;">
            <li id="req-length" class="text-muted">{{ __('adminlte::adminlte.password_requirements.length') }}</li>
            <li id="req-special" class="text-muted">{{ __('adminlte::adminlte.password_requirements.special') }}</li>
            <li id="req-number" class="text-muted">{{ __('adminlte::adminlte.password_requirements.number') }}</li>
        </ul>

        {{-- Confirm password --}}
        <div class="input-group mb-3">
            <input type="password" id="password_confirmation" name="password_confirmation"
                class="form-control @error('password_confirmation') is-invalid @enderror"
                placeholder="{{ __('adminlte::adminlte.retype_password') }}" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <a href="#" class="text-reset toggle-password" data-target="password_confirmation">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
            @error('password_confirmation')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
            <span class="fas fa-user-plus"></span>
            {{ __('adminlte::adminlte.register') }}
        </button>
    </form>
@endsection

@section('auth_footer')
    <p class="my-0">
        <a href="{{ $loginUrl }}">
            {{ __('adminlte::adminlte.i_already_have_a_membership') }}
        </a>
    </p>

        {{-- Back button --}}
    <div class="mt-3 text-center">
        <a href="{{ url('/login') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> {{ __('adminlte::adminlte.back') }}
        </a>
    </div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput = document.getElementById('password');
        const reqLength = document.getElementById('req-length');
        const reqSpecial = document.getElementById('req-special');
        const reqNumber = document.getElementById('req-number');

        passwordInput.addEventListener('input', function () {
            const value = passwordInput.value;

            const lengthValid = value.length >= 8;
            const specialValid = /[.¡!@#$%^&*()_+-]/.test(value);
            const numberValid = /\d/.test(value);

            toggleClass(reqLength, lengthValid);
            toggleClass(reqSpecial, specialValid);
            toggleClass(reqNumber, numberValid);
        });

        function toggleClass(element, isValid) {
            element.classList.remove('text-success', 'text-muted');
            element.classList.add(isValid ? 'text-success' : 'text-muted');
        }

        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(toggle => {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    });
</script>
@endpush
