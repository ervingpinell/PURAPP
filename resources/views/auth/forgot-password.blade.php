@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('dashboard_url', '/')
@section('title', __('adminlte::auth.password_reset_message'))
@section('auth_header', __('adminlte::auth.password_reset_message'))

@section('auth_body')

    @if (session('status'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-1"></i>
            {{ __('adminlte::auth.passwords.sent') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-1"></i>
            {{ __('adminlte::validation.validation_error_title') }}
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li class="srv-error">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="input-group mb-3">
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="form-control @error('email') is-invalid @enderror"
                placeholder="{{ __('adminlte::validation.attributes.email') }}"
                autocomplete="email"
                autofocus
            >
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
            @error('email')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100">
            <i class="fas fa-paper-plane me-1"></i>
            {{ __('adminlte::auth.send_password_reset_link') }}
        </button>
    </form>
@endsection

@section('auth_footer')
    <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('login') }}">{{ __('adminlte::auth.back_to_login') }}</a>
        @include('partials.language-switcher')
    </div>
@endsection
