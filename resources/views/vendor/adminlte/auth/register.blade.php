{{-- resources/views/vendor/adminlte/auth/register.blade.php --}}
@extends('adminlte::auth.auth-page', ['authType' => 'register'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@push('css')
<style>
    .form-group .invalid-feedback { display:block; }
    .input-group .input-group-text { min-width: 42px; justify-content: center; }
    #password-requirements { font-size: .875rem; list-style: none; padding-left: 1rem; }
</style>
@endpush

@php
    $loginUrl    = View::getSection('login_url')    ?? config('adminlte.login_url', 'login');
    $registerUrl = View::getSection('register_url') ?? config('adminlte.register_url', 'register');

    if (config('adminlte.use_route_url', false)) {
        $loginUrl    = $loginUrl    ? route($loginUrl)    : '';
        $registerUrl = $registerUrl ? route($registerUrl) : '';
    } else {
        $loginUrl    = $loginUrl    ? url($loginUrl)    : '';
        $registerUrl = $registerUrl ? url($registerUrl) : '';
    }

    // Para rehidratar old('phone') en formato +CCC########
    $oldPhone = old('phone');
    $oldCc = null; $oldLocal = null;
    if ($oldPhone && preg_match('/^\+(\d{1,4})(\d{3,})$/', $oldPhone, $m)) {
        $oldCc   = '+' . $m[1];
        $oldLocal = $m[2];
    }
@endphp

@section('title', __('adminlte::adminlte.register'))
@section('auth_header', __('adminlte::adminlte.register_message'))

@section('auth_body')

    @if ($errors->any())
        <div id="server-errors" class="alert alert-danger">
            <h5 class="mb-2">
                <i class="icon fas fa-exclamation-triangle"></i>
                {{ __('adminlte::validation.validation_error_title') }}
            </h5>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li class="srv-error">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="registerForm" action="{{ $registerUrl }}" method="POST" novalidate>
        @csrf

        {{-- Full name --}}
        <div class="form-group mb-3">
            <div class="input-group">
                <input
                    type="text"
                    name="full_name"
                    class="form-control @error('full_name') is-invalid @enderror"
                    value="{{ old('full_name') }}"
                    placeholder="{{ __('adminlte::validation.attributes.full_name') }}"
                    autocomplete="name"
                >
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-user {{ config('adminlte.classes_auth_icon', '') }}"></span>
                    </div>
                </div>
            </div>
            @error('full_name')
                <div class="invalid-feedback srv" data-for="full_name">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="form-group mb-3">
            <div class="input-group">
                <input
                    type="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                    placeholder="{{ __('adminlte::validation.attributes.email') }}"
                    autocomplete="email"
                >
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                    </div>
                </div>
            </div>
            @error('email')
                <div class="invalid-feedback srv" data-for="email">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Teléfono: un solo select (sin “salto”) + número local --}}
        <div class="form-group mb-3">

            <div class="input-group">
                <select id="phone_cc" class="form-select" style="max-width: 180px;">
                    @include('partials.country-codes') {{-- value=+code ; text inicia como "(+code)" --}}
                </select>

                <input
                    type="tel"
                    id="phone_local"
                    class="form-control @error('phone') is-invalid @enderror"
                    value="{{ $oldLocal ?? '' }}"
                    placeholder="{{ __('adminlte::validation.attributes.phone') }}"
                    autocomplete="tel"
                    inputmode="tel"
                >
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-phone {{ config('adminlte.classes_auth_icon', '') }}"></span>
                    </div>
                </div>

                {{-- Hidden con E.164 que se envía al backend --}}
                <input type="hidden" name="phone" id="phone_full" value="{{ old('phone') }}">
            </div>

            @error('phone')
                <div class="invalid-feedback srv" data-for="phone">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group mb-1">
            <div class="input-group">
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="{{ __('adminlte::validation.attributes.password') }}"
                    autocomplete="new-password"
                >
                <div class="input-group-append">
                    <div class="input-group-text">
                        <a href="#" class="text-reset toggle-password" data-target="password"
                           aria-label="{{ __('adminlte::adminlte.show_password') ?? 'Mostrar contraseña' }}">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
            </div>
            @error('password')
                <div class="invalid-feedback srv" data-for="password">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Requisitos de contraseña (guía visual) --}}
        <ul id="password-requirements" class="mb-3">
            <li id="req-length"  class="text-muted">{{ __('adminlte::validation.password_requirements.length') }}</li>
            <li id="req-special" class="text-muted">{{ __('adminlte::validation.password_requirements.special') }}</li>
            <li id="req-number"  class="text-muted">{{ __('adminlte::validation.password_requirements.number') }}</li>
        </ul>

        {{-- Confirm password --}}
        <div class="form-group mb-3">
            <div class="input-group">
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-control @error('password_confirmation') is-invalid @enderror"
                    placeholder="{{ __('adminlte::validation.attributes.password_confirmation') }}"
                    autocomplete="new-password"
                >
                <div class="input-group-append">
                    <div class="input-group-text">
                        <a href="#" class="text-reset toggle-password" data-target="password_confirmation"
                           aria-label="{{ __('adminlte::adminlte.show_password') ?? 'Mostrar contraseña' }}">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
            </div>
            @error('password_confirmation')
                <div class="invalid-feedback srv" data-for="password_confirmation">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn w-100 text-nowrap {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
            <span class="fas fa-user-plus me-2"></span>
            {{ __('adminlte::adminlte.register') }}
        </button>
    </form>
@endsection

@section('auth_footer')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <p class="mb-0">
                <a href="{{ $loginUrl }}">
                    {{ __('adminlte::adminlte.i_already_have_a_membership') }}
                </a>
            </p>
        </div>
        <div>@include('partials.language-switcher')</div>
    </div>

    <div class="mt-3 text-center">
        <a href="{{ url('/login') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> {{ __('adminlte::adminlte.back') }}
        </a>
    </div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
  // ---- Mostrar/ocultar contraseñas
  document.querySelectorAll('.toggle-password').forEach(toggle => {
    toggle.addEventListener('click', function (e) {
      e.preventDefault();
      const targetId = this.getAttribute('data-target');
      const input = document.getElementById(targetId);
      const icon = this.querySelector('i');
      if (!input) return;
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
      }
    });
  });

  // ---- Requisitos de contraseña (guía visual)
  const passwordInput = document.getElementById('password');
  const reqLength  = document.getElementById('req-length');
  const reqSpecial = document.getElementById('req-special');
  const reqNumber  = document.getElementById('req-number');
  function markReq(el, ok) {
    if (!el) return;
    el.classList.toggle('text-success', ok);
    el.classList.toggle('text-muted', !ok);
  }
  if (passwordInput) {
    passwordInput.addEventListener('input', function () {
      const v = passwordInput.value || '';
      markReq(reqLength,  v.length >= 8);
      markReq(reqSpecial, /[.:!@#$%^&*()_+\-]/.test(v));
      markReq(reqNumber,  /\d/.test(v));
    });
  }

  // ---- Teléfono: un solo select con type-ahead por país/código + E.164
  const cc    = document.getElementById('phone_cc');
  const local = document.getElementById('phone_local');
  const full  = document.getElementById('phone_full');
  if (!cc || !local || !full) return;

  const onlyDigits = (s) => (s || '').replace(/\D+/g, '');
  const normalize = (s) =>
    (s || '')
      .toString()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .toLowerCase();

  function updateE164() { full.value = (cc.value || '') + onlyDigits(local.value || ''); }

  // Etiquetas colapsadas/expandidas
  let expanded = false;
  function expandLabels() {
    Array.from(cc.options).forEach(opt => {
      const name = opt.dataset.name || '';
      const code = opt.dataset.code || opt.value;
      opt.text = `${name} (${code})`;
    });
    expanded = true;
  }
  function collapseLabels() {
    Array.from(cc.options).forEach(opt => {
      const code = opt.dataset.code || opt.value;
      opt.text = `(${code})`;
    });
    expanded = false;
  }

  // Type-ahead
  let nameBuf = '';
  let codeBuf = '';
  let lastTypeTs = 0;
  const BUF_WINDOW_MS = 800;
  function resetBuffers(){ nameBuf = ''; codeBuf = ''; }

  function searchAndSelect() {
    if (codeBuf) {
      const want = codeBuf;
      const idx = Array.from(cc.options).findIndex(opt => {
        const raw = opt.dataset.code || opt.value || '';
        const cmp = raw.replace(/[^\d]/g, '');
        return cmp.startsWith(want);
      });
      if (idx >= 0) { cc.selectedIndex = idx; return true; }
    }
    if (nameBuf) {
      const want = normalize(nameBuf);
      const idx = Array.from(cc.options).findIndex(opt => {
        const nm = normalize(opt.dataset.name || '');
        return nm.startsWith(want);
      });
      if (idx >= 0) { cc.selectedIndex = idx; return true; }
    }
    return false;
  }

  cc.addEventListener('mousedown', () => { expandLabels(); resetBuffers(); });
  cc.addEventListener('focus',     () => { expandLabels(); resetBuffers(); });

  cc.addEventListener('keydown', (e) => {
    const now = Date.now();
    if (now - lastTypeTs > BUF_WINDOW_MS) resetBuffers();
    lastTypeTs = now;

    if (e.key === 'Escape') { e.preventDefault(); collapseLabels(); return; }
    if (e.key === 'Enter' || e.key === ' ') return;

    if (/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ]$/.test(e.key)) {
      e.preventDefault();
      nameBuf += e.key;
      codeBuf = '';
      searchAndSelect();
      return;
    }
    if (/^[0-9+]$/.test(e.key)) {
      e.preventDefault();
      const add = e.key === '+' ? '' : e.key;
      codeBuf += add;
      nameBuf = '';
      searchAndSelect();
      return;
    }
    if (e.key === 'Backspace') {
      e.preventDefault();
      if (codeBuf) codeBuf = codeBuf.slice(0, -1);
      else if (nameBuf) nameBuf = nameBuf.slice(0, -1);
      searchAndSelect();
    }
  });

  cc.addEventListener('change', () => { collapseLabels(); updateE164(); });
  cc.addEventListener('blur',   () => { collapseLabels(); resetBuffers(); });

  local.addEventListener('input', updateE164);
  document.getElementById('registerForm').addEventListener('submit', updateE164);

  // Inicializa (selecciona old('phone') si viene)
  (function init(){
    const old = full.value;
    if (old) {
      const m = old.match(/^\+\d{1,4}/);
      if (m) {
        const wanted = m[0];
        const found = Array.from(cc.options).find(o => o.value === wanted);
        if (found) cc.value = wanted;
      }
    }
    collapseLabels(); // visible: solo "(+código)"
    updateE164();
  })();
});
</script>
@endpush
