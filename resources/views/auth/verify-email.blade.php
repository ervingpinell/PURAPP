@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('title', __('adminlte::auth.verify.verify_email_title'))

@section('auth_header')
    <i class="fas fa-envelope-open-text me-2"></i>
    {{ __('adminlte::auth.verify.verify_email_header') }}
@endsection

@section('auth_body')

    {{-- Mensaje de éxito (reenvío, etc.) --}}
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            <i class="fas fa-check-circle me-1"></i>
            {{ session('status') }}
        </div>
    @endif

    {{-- Texto principal --}}
    <p class="mb-2">
        {{ __('adminlte::auth.verify.message') }}
    </p>

    {{-- Botón para reenviar (usa la ruta que tienes definida: verification.send) --}}
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn btn-primary btn-block">
            <i class="fas fa-paper-plane me-1"></i>
            {{ __('adminlte::auth.verify.resend') }}
        </button>
    </form>
@endsection

@section('auth_footer')
    <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            {{ __('adminlte::auth.back_to_login') }}
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>

        @include('partials.language-switcher')
    </div>
@endsection
