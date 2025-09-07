@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('title', __('adminlte::auth.confirm_password'))
@section('auth_header')
  <h3 class="text-center mb-0">
    <i class="fas fa-lock me-2"></i>{{ __('adminlte::auth.confirm_password') }}
  </h3>
@stop

@push('css')
<style>
  .input-group .input-group-text { min-width: 42px; justify-content: center; }
</style>
@endpush

@section('auth_body')

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

  <p class="text-muted mb-3">
    {{ __('Por seguridad, confirma tu contraseña para continuar.') }}
  </p>

  <form method="POST" action="{{ route('password.confirm') }}">
    @csrf

    <div class="input-group mb-3">
      <input type="password"
             name="password"
             id="password"
             class="form-control @error('password') is-invalid @enderror"
             placeholder="{{ __('adminlte::validation.attributes.password') }}"
             autocomplete="current-password"
             autofocus>
      <div class="input-group-append">
        <div class="input-group-text">
          <a href="#" class="text-reset toggle-password" data-target="password" aria-label="{{ __('adminlte::adminlte.show_password') }}">
            <i class="fas fa-eye"></i>
          </a>
        </div>
      </div>
      @error('password')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
      @enderror
    </div>

    <button type="submit" class="btn btn-primary w-100">
      <i class="fas fa-check me-2"></i>{{ __('adminlte::auth.confirm_password') }}
    </button>
  </form>
@endsection

@section('auth_footer')
  <div class="d-flex justify-content-between align-items-center">
    <a href="{{ url()->previous() }}">{{ __('adminlte::adminlte.back') }}</a>
    @include('partials.language-switcher')
  </div>
@endsection

@section('adminlte_js')
<script>
document.addEventListener('DOMContentLoaded', function () {
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
});
</script>
@endsection
