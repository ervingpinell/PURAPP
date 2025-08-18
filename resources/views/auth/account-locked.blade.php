@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('title', __('adminlte::auth.account.locked_title'))

@section('auth_header')
    <i class="fas fa-exclamation-triangle me-2"></i>
    {{ __('adminlte::auth.account.locked_title') }}
@endsection

@section('auth_body')
    {{-- Verde: solo si realmente se envió el enlace (el controlador pone status) --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible">
            <i class="fas fa-check-circle me-1"></i>
            {{ session('status') }}
        </div>
    @endif

    {{-- Mensaje de bloqueo --}}
    @if (session('locked_message'))
        <div class="alert alert-danger">
            {{ session('locked_message') }}
        </div>
    @else
        <div class="alert alert-danger">
            {{ __('adminlte::auth.account.locked_message') }}
        </div>
    @endif

    @php $retry = (int) session('retry_seconds', 0); @endphp

    {{-- Sugerencia de espera (si aplica) --}}
    @if ($retry > 0)
        <p class="small text-muted mb-0">
            {{ __('Intentos permitidos nuevamente en :secs segundos.', ['secs' => $retry]) }}
        </p>
    @endif

    {{-- Sin botón de reenvío ni formulario (el controlador ya envía con throttling) --}}
@endsection

@section('auth_footer')
    <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('login') }}">{{ __('adminlte::adminlte.back_to_login') }}</a>
        @include('partials.language-switcher')
    </div>
@endsection
