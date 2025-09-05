{{-- resources/views/auth/two-factor-challenge.blade.php --}}
@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('title', 'Two-Factor Challenge')
@section('auth_header', 'Verificación en dos pasos')

@push('css')
<style>
  /* Compactar la tarjeta y centrar todo */
  .login-box, .register-box {
    width: 420px;
    max-width: 94%;
  }
  /* Logo más pulido */
  .login-logo {
    text-align: center !important;
    margin-bottom: .75rem !important;
  }
  .login-logo img {
    display: block;
    margin: 0 auto;
    max-height: 64px;
    width: auto;
    object-fit: contain;
  }
  /* Íconos alineados y del mismo ancho */
  .input-group .input-group-text { min-width: 42px; justify-content: center; }
  /* Bordes y aspecto sutil */
  .card { border-radius: .75rem; }
</style>
@endpush

@section('auth_body')

  {{-- Errores sencillos arriba --}}
  @if ($errors->any())
    <div class="alert alert-danger">
      <i class="fas fa-exclamation-triangle me-1"></i>
      {{ $errors->first() }}
    </div>
  @endif

  {{-- Mensajes de estado (opcional) --}}
  @if (session('status'))
    @php
      $map = [
        'two-factor-authentication-enabled'   => 'Autenticación en dos pasos habilitada.',
        'two-factor-authentication-confirmed' => 'Autenticación en dos pasos confirmada.',
        'two-factor-authentication-disabled'  => 'Autenticación en dos pasos desactivada.',
        'recovery-codes-generated'            => 'Nuevos códigos de recuperación generados.',
      ];
    @endphp
    <div class="alert alert-success">{{ $map[session('status')] ?? session('status') }}</div>
  @endif

  <form method="POST" action="{{ route('two-factor.login') }}">
    @csrf

    {{-- Código TOTP --}}
    <div class="input-group mb-3">
      <input
        type="text"
        name="code"
        inputmode="numeric"
        pattern="[0-9]*"
        maxlength="6"
        autocomplete="one-time-code"
        class="form-control"
        placeholder="Código 123456"
        autofocus
      >
      <div class="input-group-append">
        <div class="input-group-text"><span class="fas fa-key"></span></div>
      </div>
    </div>

    <div class="text-center text-muted mb-2">— o —</div>

    {{-- Código de recuperación --}}
    <div class="input-group mb-3">
      <input
        type="text"
        name="recovery_code"
        class="form-control"
        placeholder="Código de recuperación"
      >
      <div class="input-group-append">
        <div class="input-group-text"><span class="fas fa-life-ring"></span></div>
      </div>
    </div>

    {{-- Recordar este dispositivo (cookie de “recuerda este dispositivo” de Fortify) --}}
    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" name="remember" id="remember">
      <label class="form-check-label" for="remember">
        Recordar este dispositivo
      </label>
    </div>

    <button type="submit" class="btn btn-primary w-100">
      <i class="fas fa-shield-alt me-1"></i> Verificar
    </button>
  </form>
@endsection

@section('auth_footer')
  @include('partials.language-switcher')
@endsection
