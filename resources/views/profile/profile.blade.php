{{-- resources/views/profile/edit.blade.php --}}

@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@php
    $updateUrl = route('profile.update');
    $homeUrl = route('home');
@endphp

@section('title', __('adminlte::adminlte.edit_profile'))

@section('auth_header')
    <h3 class="text-center mb-0">{{ __('adminlte::adminlte.edit_profile') }}</h3>
@stop

@section('auth_body')
    @if(session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ $updateUrl }}" method="POST" novalidate>
        @csrf

        {{-- Nombre Completo --}}
        <div class="input-group mb-3">
            <input type="text" name="full_name" id="full_name"
                class="form-control @error('full_name') is-invalid @enderror"
                value="{{ old('full_name', auth()->user()->full_name) }}"
                placeholder="{{ __('adminlte::adminlte.full_name') }}" required autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('full_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Email --}}
        <div class="input-group mb-3">
            <input type="email" name="email" id="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', auth()->user()->email) }}"
                placeholder="{{ __('adminlte::adminlte.email') }}" required>

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

        {{-- Teléfono --}}
        <div class="input-group mb-3">
            <input type="text" name="phone" id="phone"
                class="form-control @error('phone') is-invalid @enderror"
                value="{{ old('phone', auth()->user()->phone) }}"
                placeholder="{{ __('adminlte::adminlte.phone') }}">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-phone {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('phone')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Nueva contraseña --}}
        <div class="input-group mb-3">
            <input type="password" name="password" id="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="{{ __('adminlte::adminlte.password') }} ({{ __('adminlte::adminlte.optional') }})">

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

        {{-- Confirmar contraseña --}}
        <div class="input-group mb-3">
            <input type="password" name="password_confirmation" id="password_confirmation"
                class="form-control"
                placeholder="{{ __('adminlte::adminlte.retype_password') }}">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span id="toggle-password-confirmation" class="fas fa-eye {{ config('adminlte.classes_auth_icon', '') }}" style="cursor: pointer;"></span>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-block btn-info">
            <i class="fas fa-save"></i> {{ __('adminlte::adminlte.save') }}
        </button>
    </form>
@stop

@section('auth_footer')
    {{-- Enlaces a la izquierda + idioma a la derecha --}}
    <div class="d-flex justify-content-between align-items-center">
        <div>
            {{-- Aquí podrías poner enlaces o dejar vacío si no hay --}}
        </div>
        <div>
            @include('partials.language-switcher')
        </div>
    </div>

    <div class="mt-3 text-center">
        <a href="{{ $homeUrl }}" class="text-muted"><i class="fas fa-arrow-left"></i> {{ __('adminlte::adminlte.back') }}</a>
    </div>
@stop


@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('toggle-password');
        const passwordConfirmationInput = document.getElementById('password_confirmation');
        const togglePasswordConfirmation = document.getElementById('toggle-password-confirmation');

        function toggleVisibility(input, toggleIcon) {
            if (input.type === 'password') {
                input.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        togglePassword.addEventListener('click', function () {
            toggleVisibility(passwordInput, togglePassword);
        });

        togglePasswordConfirmation.addEventListener('click', function () {
            toggleVisibility(passwordConfirmationInput, togglePasswordConfirmation);
        });
    });
</script>
@endpush
