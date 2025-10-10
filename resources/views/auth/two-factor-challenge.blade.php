{{-- resources/views/auth/two-factor-challenge.blade.php --}}
@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@php
  // Helper con fallback para evitar ver "auth.xyz" cuando falte la key
  $t = fn($key, $fallback) => __($key) !== $key ? __($key) : $fallback;

  $pageTitle   = $t('auth.two_factor.title', 'Two-Factor Challenge');
  $pageHeader  = $t('auth.two_factor.header', 'Verificación en dos pasos');

  $labelCode      = $t('auth.two_factor.code', 'Código de autenticación');
  $phCode         = $t('auth.two_factor.enter_code', 'Introduce el código de 6 dígitos');
  $labelRecovery  = $t('auth.two_factor.recovery_code', 'Código de recuperación');
  $useRecoveryTxt = $t('auth.two_factor.use_recovery', 'Usar un código de recuperación');
  $btnConfirm     = $t('auth.two_factor.confirm', 'Confirmar');

  // Mapear los status que Fortify setea en session('status')
  $statusMap = [
    'two-factor-authentication-enabled'   => $t('auth.two_factor.enabled', 'Autenticación en dos pasos activada.'),
    'two-factor-authentication-confirmed' => $t('auth.two_factor.confirmed', 'Autenticación en dos pasos confirmada.'),
    'two-factor-authentication-disabled'  => $t('auth.two_factor.disabled', 'Autenticación en dos pasos desactivada.'),
    'recovery-codes-generated'            => $t('auth.two_factor.recovery_codes_generated', 'Se generaron nuevos códigos de recuperación.'),
  ];
@endphp

@section('title', $pageTitle)
@section('auth_header', $pageHeader)

@push('css')
<style>
  .login-box, .register-box { width: 420px; max-width: 94%; }
  .login-logo { text-align: center !important; margin-bottom: .75rem !important; }
  .login-logo img { display:block; margin:0 auto; max-height:64px; width:auto; object-fit:contain; }
  .input-group .input-group-text { min-width: 42px; justify-content: center; }
  .card { border-radius: .75rem; }
</style>
@endpush

@section('auth_body')

  {{-- Errores arriba --}}
  @if ($errors->any())
    <div class="alert alert-danger">
      <i class="fas fa-exclamation-triangle me-1"></i>
      {{ $errors->first() }}
    </div>
  @endif

  {{-- Mensajes de estado (Fortify) --}}
  @if (session('status'))
    <div class="alert alert-success">
      {{ $statusMap[session('status')] ?? session('status') }}
    </div>
  @endif

  <form method="POST" action="{{ route('two-factor.login') }}" novalidate>
    @csrf

    {{-- Código TOTP --}}
    <div class="mb-1 small text-muted">{{ $labelCode }}</div>
    <div class="input-group mb-3">
      <input
        type="text"
        name="code"
        inputmode="numeric"
        pattern="[0-9]*"
        maxlength="6"
        autocomplete="one-time-code"
        class="form-control @error('code') is-invalid @enderror"
        placeholder="{{ $phCode }}"
        autofocus
      >
      <div class="input-group-append">
        <div class="input-group-text"><span class="fas fa-key"></span></div>
      </div>
      @error('code')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
      @enderror
    </div>

    <div class="text-center text-muted mb-2">— {{ $useRecoveryTxt }} —</div>

    {{-- Código de recuperación --}}
    <div class="mb-1 small text-muted">{{ $labelRecovery }}</div>
    <div class="input-group mb-3">
      <input
        type="text"
        name="recovery_code"
        class="form-control @error('recovery_code') is-invalid @enderror"
        autocomplete="one-time-code"
        placeholder="{{ $labelRecovery }}"
      >
      <div class="input-group-append">
        <div class="input-group-text"><span class="fas fa-life-ring"></span></div>
      </div>
      @error('recovery_code')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
      @enderror
    </div>


    <button type="submit" class="btn btn-primary w-100">
      <i class="fas fa-shield-alt me-1"></i> {{ $btnConfirm }}
    </button>
  </form>
@endsection

@section('auth_footer')
  @include('partials.language-switcher')
@endsection
