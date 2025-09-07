@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('dashboard_url', '/')
@section('title', __('adminlte::auth.reset_password'))
@section('auth_header', __('adminlte::auth.reset_password'))

@push('css')
<style>
  .input-group .input-group-text { min-width: 42px; justify-content: center; }
  #password-requirements { font-size:.875rem; list-style:none; padding-left:1rem; margin-top:.5rem; }
  #password-requirements li { transition: color .15s ease-in-out; }
</style>
@endpush

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

  <form method="POST" action="{{ route('password.update') }}" novalidate>
    @csrf

    {{-- Requeridos por Fortify --}}
    <input type="hidden" name="token" value="{{ $request->route('token') }}">
    <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

    {{-- Email (solo lectura) --}}
    <div class="form-group mb-3">
      <div class="input-group">
        <input
          type="email"
          class="form-control"
          value="{{ old('email', $request->email) }}"
          placeholder="{{ __('adminlte::validation.attributes.email') }}"
          readonly
        >
        <div class="input-group-append">
          <div class="input-group-text"><span class="fas fa-envelope"></span></div>
        </div>
      </div>
    </div>

    {{-- Nueva contraseña --}}
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
            <a href="#" class="text-reset toggle-password" data-target="password" aria-label="{{ __('adminlte::adminlte.show_password') }}">
              <i class="fas fa-eye"></i>
            </a>
          </div>
        </div>
      </div>
      @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Requisitos con IDs correctos --}}
    <ul id="password-requirements" class="mb-3 small text-muted">
      <li id="req-length" >{{ __('adminlte::validation.password_requirements.length') }}</li>
      <li id="req-special">{{ __('adminlte::validation.password_requirements.special') }}</li>
      <li id="req-number" >{{ __('adminlte::validation.password_requirements.number') }}</li>
    </ul>

    {{-- Confirmación --}}
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
            <a href="#" class="text-reset toggle-password" data-target="password_confirmation" aria-label="{{ __('adminlte::adminlte.show_password') }}">
              <i class="fas fa-eye"></i>
            </a>
          </div>
        </div>
      </div>
      @error('password_confirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <button type="submit" class="btn btn-primary w-100">
      <i class="fas fa-unlock-alt me-2"></i>
      {{ __('adminlte::auth.reset_password') }}
    </button>
  </form>
@endsection

@section('auth_footer')
  <div class="d-flex justify-content-between align-items-center">
    <a href="{{ route('login') }}">{{ __('adminlte::auth.back_to_login') }}</a>
    @include('partials.language-switcher')
  </div>
@endsection

@section('adminlte_js')
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Mostrar/ocultar contraseña
  document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', function(e){
      e.preventDefault();
      const input = document.getElementById(this.dataset.target);
      const icon  = this.querySelector('i');
      if (!input) return;
      if (input.type === 'password') { input.type = 'text'; icon.classList.replace('fa-eye','fa-eye-slash'); }
      else { input.type = 'password'; icon.classList.replace('fa-eye-slash','fa-eye'); }
    });
  });

  // Hints de requisitos
  const pwd    = document.getElementById('password');
  const reqLen = document.getElementById('req-length');
  const reqSp  = document.getElementById('req-special');
  const reqNum = document.getElementById('req-number');

  function mark(el, ok){
    if (!el) return;
    el.classList.toggle('text-success', ok);
    el.classList.toggle('text-muted', !ok);
  }

  function checkPasswordHints(){
    const v = (pwd.value || '');
    mark(reqLen, v.length >= 8);
    // incluye "¡" (U+00A1) dentro del set de especiales
    mark(reqSp,  /[.\u00A1!@#$%^&*()_+\-]/.test(v));
    mark(reqNum, /\d/.test(v));
  }

  if (pwd) {
    pwd.addEventListener('input', checkPasswordHints);
    checkPasswordHints(); // estado inicial
  }
});
</script>
@endsection
