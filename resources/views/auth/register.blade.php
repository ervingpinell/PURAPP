    {{-- No Se usa --}}
    
@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@php
    $loginUrl = View::getSection('login_url') ?? config('adminlte.login_url', 'login');

    if (config('adminlte.use_route_url', false)) {
        $loginUrl = $loginUrl ? route($loginUrl) : '';
    } else {
        $loginUrl = $loginUrl ? url($loginUrl) : '';
    }
@endphp

@section('title', __('adminlte::adminlte.register'))
@section('auth_header', __('adminlte::adminlte.register_message'))

@section('auth_body')
    <form action="{{ $registerUrl }}" method="post">
        @csrf

        <div class="mb-3">
            <input type="text" name="full_name" class="form-control" placeholder="{{ __('adminlte::adminlte.full_name') }}" value="{{ old('full_name') }}" required autofocus>
        </div>

        <div class="mb-3">
            <input type="email" name="email" class="form-control" placeholder="{{ __('adminlte::adminlte.email') }}" value="{{ old('email') }}" required>
        </div>

        <div class="mb-3">
            <input type="text" name="phone" class="form-control" placeholder="{{ __('adminlte::adminlte.phone') }}" value="{{ old('phone') }}" required>
        </div>

        <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="{{ __('adminlte::adminlte.password') }}" required>
        </div>

        <div class="mb-3">
            <input type="password" name="password_confirmation" class="form-control" placeholder="{{ __('adminlte::adminlte.retype_password') }}" required>
        </div>

        <button type="submit" class="btn btn-primary w-100 text-nowrap">
            <span class="fas fa-user-plus me-2"></span>
            {{ __('adminlte::adminlte.register') }}
        </button>
    </form>
@stop

@section('auth_footer')
    {{-- Enlaces a la izquierda + idioma a la derecha --}}
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <p class="mb-0">
                <a href="{{ $loginUrl }}">
                    {{ __('adminlte::adminlte.i_already_have_a_membership') }}
                </a>
            </p>
        </div>

        <div>
            @include('partials.language-switcher')
        </div>
    </div>

    {{-- Botón regresar --}}
    <div class="mt-3 text-center">
        <a href="{{ url('/login') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> {{ __('adminlte::adminlte.back') }}
        </a>
    </div>
@endsection
