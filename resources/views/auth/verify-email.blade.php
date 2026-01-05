@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('title', __('adminlte::auth.verify.verify_email_title'))

@section('auth_header')
<i class="fas fa-envelope-open-text me-2"></i>
{{ __('adminlte::auth.verify.verify_email_header') }}
@endsection

@section('auth_body')

@php
$registeredEmail = session('registered_email');
@endphp

@if (session('status'))
<div class="alert alert-success" role="alert">
    <i class="fas fa-check-circle me-1"></i>
    {{ session('status') }}
</div>
@endif

@if ($registeredEmail)
<div class="alert alert-info" role="alert">
    <i class="fas fa-info-circle me-1"></i>
    {{ __('adminlte::auth.verify.sent_to', ['email' => $registeredEmail]) }}
</div>
@endif

<p class="mb-3">
    {{ __('adminlte::auth.verify.verify_email_instructions') }}
</p>

{{-- Botón para mostrar el formulario de reenvío --}}
<div id="resend-button-container" class="mb-3">
    <button type="button" class="btn btn-outline-primary btn-block" id="show-resend-form">
        <i class="fas fa-redo me-1"></i>
        {{ __('adminlte::auth.verify.resend') }}
    </button>
</div>

{{-- Formulario de reenvío (oculto inicialmente) --}}
<form method="POST" action="{{ route('verification.public.resend') }}" id="resend-form" style="display: none;">
    @csrf

    @if ($registeredEmail)
    {{-- Si tenemos el correo en sesión, lo mandamos oculto --}}
    <input type="hidden" name="email" value="{{ $registeredEmail }}">
    @else
    {{-- Fallback: permitir al usuario ingresar su correo manualmente --}}
    <div class="form-group mb-3">
        <label for="email">{{ __('adminlte::auth.verify.email_label') }}</label>
        <input
            type="email"
            id="email"
            name="email"
            class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email') }}"
            required
            autocomplete="email">
        @error('email')
        <span class="invalid-feedback d-block" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    @endif

    <button type="submit" class="btn btn-primary btn-block">
        <i class="fas fa-paper-plane me-1"></i>
        {{ __('adminlte::auth.verify.resend') }}
    </button>

    <button type="button" class="btn btn-link btn-block mt-2" id="hide-resend-form">
        {{ __('adminlte::adminlte.cancel') }}
    </button>
</form>
@endsection

@section('auth_footer')
<div class="d-flex justify-content-between align-items-center">
    <a href="{{ route('login') }}">
        {{ __('adminlte::auth.back_to_login') }}
    </a>

    @include('partials.language-switcher')
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const showButton = document.getElementById('show-resend-form');
        const hideButton = document.getElementById('hide-resend-form');
        const resendForm = document.getElementById('resend-form');
        const buttonContainer = document.getElementById('resend-button-container');

        if (showButton && resendForm && buttonContainer) {
            showButton.addEventListener('click', function() {
                buttonContainer.style.display = 'none';
                resendForm.style.display = 'block';
            });

            if (hideButton) {
                hideButton.addEventListener('click', function() {
                    resendForm.style.display = 'none';
                    buttonContainer.style.display = 'block';
                });
            }
        }
    });
</script>
@endpush