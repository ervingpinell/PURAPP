{{-- resources/views/auth/two-factor-challenge.blade.php --}}
@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@php
// Helper de traducción con fallback
$t = fn($key, $fallback) => __($key) !== $key ? __($key) : $fallback;

// Textos (si quieres forzar EN, cambia los fallbacks a inglés)
$pageTitle = $t('auth.two_factor.title', 'Two-Factor Challenge');
$pageHeader = $t('auth.two_factor.header', 'Two-Factor Verification');

$labelCode = $t('auth.two_factor.code', 'Authentication code');
$phCode = $t('auth.two_factor.enter_code', 'Enter the 6-digit code');
$labelRecovery = $t('auth.two_factor.recovery_code', 'Recovery code');
$useRecoveryTxt = $t('auth.two_factor.use_recovery', 'Use a recovery code');
$btnConfirm = $t('auth.two_factor.confirm', 'Confirm');

// Mensajes Fortify (status)
$statusMap = [
'two-factor-authentication-enabled' => $t('auth.two_factor.enabled', 'Two-factor authentication enabled.'),
'two-factor-authentication-confirmed' => $t('auth.two_factor.confirmed', 'Two-factor authentication confirmed.'),
'two-factor-authentication-disabled' => $t('auth.two_factor.disabled', 'Two-factor authentication disabled.'),
'recovery-codes-generated' => $t('auth.two_factor.recovery_codes_generated', 'New recovery codes generated.'),
];

// Datos para el language switcher en el footer
$currentLocale = app()->getLocale();
$locales = config('routes.locales', []);
$flags = [
'es' => 'es.svg',
'en' => 'en.svg',
'fr' => 'fr.svg',
'pt' => 'pt.svg',
'de' => 'de.svg',
];
$flag = $flags[$currentLocale] ?? 'en.svg';
@endphp

@section('title', $pageTitle)
@section('auth_header', $pageHeader)

@push('css')
<style>
  /* Estilos ligeros para el <details> del selector (footer) */
  .lang-switcher {
    position: relative;
    display: inline-block;
  }

  .lang-switcher>summary {
    list-style: none;
    cursor: pointer;
  }

  .lang-switcher>summary::-webkit-details-marker {
    display: none;
  }

  .lang-switcher .btn-lang {
    gap: .35rem;
    letter-spacing: .3px;
  }

  .lang-switcher[open] .lang-menu {
    display: block;
  }

  .lang-menu {
    display: none;
    position: absolute;
    right: 0;
    bottom: 110%;
    min-width: 180px;
    z-index: 1060;
  }

  .lang-menu a {
    color: inherit;
    text-decoration: none;
    display: flex;
    align-items: center;
    padding: .25rem .5rem;
    border-radius: .35rem;
  }

  .lang-menu a:hover {
    text-decoration: underline;
  }
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
      autofocus>
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
      placeholder="{{ $labelRecovery }}">
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
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
  {{-- Botón volver al sitio --}}
  <a href="{{ url('/') }}" class="btn btn-outline-secondary">
    <i class="fas fa-arrow-left me-1"></i> {{ $t('adminlte::adminlte.back', 'Back') }}
  </a>

  {{-- Language switcher (en footer) --}}
  <details class="lang-switcher">
    <summary class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center btn-lang">
      <img src="{{ asset('svg/flags/' . $flag) }}" alt="{{ strtoupper($currentLocale) }}" width="18" height="12">
      <span>{{ strtoupper($currentLocale) }}</span>
    </summary>
    <div class="lang-menu card p-2">
      @foreach($locales as $code => $cfg)
      @php $f = $flags[$code] ?? ($code . '.svg'); @endphp
      <a href="{{ route('switch.language', $code) }}">
        <img src="{{ asset('svg/flags/' . $f) }}" width="18" height="12" class="me-2" alt="{{ strtoupper($code) }}">
        <span>{{ $cfg['name'] ?? strtoupper($code) }}</span>
      </a>
      @endforeach
    </div>
  </details>
</div>
@endsection