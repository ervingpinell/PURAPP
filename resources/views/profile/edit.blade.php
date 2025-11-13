{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title', __('adminlte::adminlte.edit_profile'))

@push('css')
<style>
  .profile-page .card {
    border-radius: .75rem;
  }
  .input-group .input-group-text {
    min-width: 42px;
    justify-content: center;
  }
  .form-group .invalid-feedback,
  .invalid-feedback.d-block {
    display: block;
  }
  #phone_cc { max-width: 140px; }
</style>
@endpush

@section('content')
<div class="container py-5 profile-page">
  <div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">

{{-- Flashes --}}
@if(session('success') && session('success') !== __('adminlte::adminlte.profile_updated_email_change_pending'))
  <div class="alert alert-success text-center">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger text-center">{{ session('error') }}</div>
@endif
      {{-- Aviso si hay un cambio de correo pendiente --}}
      @if(!empty($user->pending_email))
        <div class="alert alert-warning">
          <h5 class="mb-1">
            <i class="fas fa-info-circle me-1"></i>
            {{ __('adminlte::adminlte.pending_email_title') }}
          </h5>
          <p class="mb-0">
            {!! __('adminlte::adminlte.pending_email_notice', [
                'current' => e($user->email),
                'pending' => e($user->pending_email),
            ]) !!}
          </p>
        </div>
      @endif

      {{-- Errores agrupados --}}
      @if ($errors->any())
        <div class="alert alert-danger">
          <h5 class="mb-2">
            <i class="icon fas fa-exclamation-triangle"></i>
            {{ __('adminlte::validation.validation_error_title') }}
          </h5>
          <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="card shadow">
        <div class="card-header text-center">
          <h3 class="card-title m-0">
            <i class="fas fa-user-edit me-2"></i>
            {{ __('adminlte::adminlte.edit_profile') }}
          </h3>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" novalidate>
          @csrf
          <div class="card-body">

            {{-- Full name --}}
            <div class="input-group mb-3">
              <input type="text" name="full_name" id="full_name"
                     class="form-control @error('full_name') is-invalid @enderror"
                     value="{{ old('full_name', $user->full_name) }}"
                     placeholder="{{ __('adminlte::validation.attributes.full_name') }}" required autofocus>
              <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-user"></span></div>
              </div>
              @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Email --}}
            <div class="input-group mb-1">
              <input type="email" name="email" id="email"
                     class="form-control @error('email') is-invalid @enderror"
                     value="{{ old('email', $user->pending_email ?? $user->email) }}"
                     placeholder="{{ __('adminlte::validation.attributes.email') }}"
                     required autocomplete="email">
              <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-envelope"></span></div>
              </div>
              @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Aviso: si cambias el correo, se enviará un enlace de confirmación --}}
            <div id="email-change-hint"
                 class="alert alert-warning py-2 px-3 mt-2 d-none"
                 style="font-size: 0.9rem;">
              <i class="fas fa-info-circle me-1"></i>
              {{ __('adminlte::adminlte.email_change_warning') }}
            </div>

            {{-- Phone --}}
            <div class="mb-3 mt-3">
              <div class="input-group">
                <select id="phone_cc" name="country_code"
                        class="form-select @error('country_code') is-invalid @enderror">
                  @include('partials.country-codes')
                </select>
                <input type="tel" name="phone" id="phone"
                       class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone', $user->phone) }}"
                       placeholder="{{ __('adminlte::validation.attributes.phone') }}"
                       inputmode="tel" autocomplete="tel">
                <div class="input-group-append">
                  <div class="input-group-text"><span class="fas fa-phone"></span></div>
                </div>
              </div>
              @error('country_code') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
              @error('phone')        <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            {{-- Password opcional --}}
            <div class="input-group mb-3">
              <input type="password" name="password" id="password"
                     class="form-control @error('password') is-invalid @enderror"
                     placeholder="{{ __('adminlte::validation.attributes.password') }} ({{ __('adminlte::adminlte.optional') ?? 'Opcional' }})"
                     autocomplete="new-password">
              <div class="input-group-append">
                <div class="input-group-text">
                  <a href="#" class="text-reset toggle-password" data-target="password">
                    <i class="fas fa-eye"></i>
                  </a>
                </div>
              </div>
              @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Confirm password --}}
            <div class="input-group mb-3">
              <input type="password" name="password_confirmation" id="password_confirmation"
                     class="form-control @error('password_confirmation') is-invalid @enderror"
                     placeholder="{{ __('adminlte::validation.attributes.password_confirmation') }}"
                     autocomplete="new-password">
              <div class="input-group-append">
                <div class="input-group-text">
                  <a href="#" class="text-reset toggle-password" data-target="password_confirmation">
                    <i class="fas fa-eye"></i>
                  </a>
                </div>
              </div>
              @error('password_confirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

          </div>
          <div class="card-footer text-center">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> {{ __('adminlte::adminlte.save_changes') }}
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
  // toggle password
  document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', function(e){
      e.preventDefault();
      const input = document.getElementById(this.dataset.target);
      const icon  = this.querySelector('i');
      if (!input) return;
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye','fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash','fa-eye');
      }
    });
  });

  // phone country code labels
  const cc = document.getElementById('phone_cc');
  if (cc) {
    function expandLabels(){
      Array.from(cc.options).forEach(opt => {
        const name = opt.dataset.name || '';
        const code = opt.dataset.code || opt.value;
        opt.textContent = `${name} (${code})`;
      });
    }
    function collapseLabels(){
      Array.from(cc.options).forEach(opt => {
        const code = opt.dataset.code || opt.value;
        opt.textContent = `(${code})`;
      });
    }
    cc.addEventListener('focus', expandLabels);
    cc.addEventListener('blur',  collapseLabels);
    collapseLabels();

    const currentCode = @json(old('country_code', $user->country_code ?? null));
    if (currentCode) cc.value = currentCode;
  }

  // aviso cuando el email es diferente al actual/pending
  const emailInput = document.getElementById('email');
  const emailHint  = document.getElementById('email-change-hint');

  if (emailInput && emailHint) {
    const originalEmail = @json($user->pending_email ?? $user->email);

    const toggleEmailHint = () => {
      const current  = (emailInput.value || '').trim().toLowerCase();
      const original = (originalEmail || '').trim().toLowerCase();

      if (current && current !== original) {
        emailHint.classList.remove('d-none');
      } else {
        emailHint.classList.add('d-none');
      }
    };

    emailInput.addEventListener('input', toggleEmailHint);
    emailInput.addEventListener('blur', toggleEmailHint);
    toggleEmailHint();
  }
});
</script>
@endpush
