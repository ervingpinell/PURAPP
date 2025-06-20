@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
   <!-- <link rel="stylesheet" href="{{ asset('css/gv.css') }}"> -->
@stop

@php
    $loginUrl = View::getSection('login_url') ?? config('adminlte.login_url', 'login');
    $registerUrl = View::getSection('register_url') ?? config('adminlte.register_url', 'register');
    $passResetUrl = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset');

    if (config('adminlte.use_route_url', false)) {
        $loginUrl     = $loginUrl     ? route($loginUrl)     : '';
        $registerUrl  = $registerUrl  ? route($registerUrl)  : '';
        $passResetUrl = $passResetUrl ? route($passResetUrl) : '';
    } else {
        $loginUrl     = $loginUrl     ? url($loginUrl)     : '';
        $registerUrl  = $registerUrl  ? url($registerUrl)  : '';
        $passResetUrl = $passResetUrl ? url($passResetUrl) : '';
    }
@endphp

@section('auth_header', __('adminlte::adminlte.login_message'))

@section('auth_body')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ $loginUrl }}" method="post">
        @csrf

        {{-- Email field --}}
        <div class="input-group mb-3">
            <input
                type="email"
                name="email"
                id="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}"
                placeholder="{{ __('adminlte::adminlte.email') }}"
                autofocus
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

        {{-- Password field --}}
        <div class="input-group mb-3">
            <input
                type="password"
                name="password"
                id="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="{{ __('adminlte::adminlte.password') }}"
            >
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

        {{-- Login button --}}
        <div class="row">
            <div class="col-12">
                <button
                    type="submit"
                    class="btn w-100 text-nowrap {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}"
                >
                    <span class="fas fa-sign-in-alt me-2"></span>
                    {{ __('adminlte::adminlte.sign_in') }}
                </button>
            </div>
        </div>
    </form>
@stop

@section('auth_footer')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            @if($passResetUrl)
                <p class="mb-1">
                    <a href="{{ $passResetUrl }}">
                        {{ __('adminlte::adminlte.i_forgot_my_password') }}
                    </a>
                </p>
            @endif

            @if($registerUrl)
                <p class="mb-0">
                    <a href="{{ $registerUrl }}">
                        {{ __('adminlte::adminlte.register_a_new_membership') }}
                    </a>
                </p>
            @endif
        </div>

        <div>
            @include('partials.language-switcher')
        </div>
    </div>

    <div class="mt-3 text-center">
        <a href="{{ url('/') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> {{ __('adminlte::adminlte.back') }}
        </a>
    </div>
@stop

@section('adminlte_js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-password').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.dataset.target;
            const input    = document.getElementById(targetId);
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                this.querySelector('i')
                    .classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                this.querySelector('i')
                    .classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });
});
</script>
@stop
