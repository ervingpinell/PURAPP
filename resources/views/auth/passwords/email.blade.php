@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('auth_header', __('adminlte::adminlte.reset_password'))

@section('auth_body')
    @if(session('status'))
        <div class="alert alert-success alert-dismissible">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="input-group mb-3">
            <input type="email"
                   name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="{{ __('adminlte::adminlte.email') }}"
                   value="{{ old('email') }}"
                   required autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
            @error('email')
                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-block">
            {{ __('adminlte::adminlte.send_password_reset_link') ?? __('Enviar enlace de recuperación') }}
        </button>
    </form>
@stop

@section('auth_footer')
    <div class="d-flex flex-column text-center gap-2">
        <div>
            <a href="{{ route('login') }}">
                {{ __('adminlte::adminlte.back_to_login') ?? __('Volver al login') }}
            </a>
        </div>
        <div>
            @include('partials.language-switcher')
        </div>
    </div>
@stop
