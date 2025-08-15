@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit_profile'))

@section('content_header')
  <h1 class="text-center">
    <i class="fas fa-user-edit"></i>
    {{ __('adminlte::adminlte.edit_profile_of', ['name' => $user->full_name]) }}
  </h1>
@stop

@section('content')
<div class="d-flex justify-content-center">
  <div class="col-md-6">
    <div class="card card-primary shadow">
      <div class="card-header text-center">
        <h3 class="card-title w-100">
          <i class="fas fa-user-cog"></i> {{ __('adminlte::adminlte.profile_information') }}
        </h3>
      </div>

      <form action="{{ route('profile.update') }}" method="POST" id="adminProfileForm" novalidate>
        @csrf

        <div class="card-body">

          {{-- Nombre --}}
          <div class="form-group mb-3">
            <label class="form-label"><i class="fas fa-user"></i> {{ __('adminlte::validation.attributes.full_name') }}</label>
            <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                   value="{{ old('full_name', $user->full_name) }}" autocomplete="name">
            @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- Email --}}
          <div class="form-group mb-3">
            <label class="form-label"><i class="fas fa-envelope"></i> {{ __('adminlte::validation.attributes.email') }}</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $user->email) }}" autocomplete="email">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- Teléfono --}}
          <div class="form-group mb-3">
            <label class="form-label"><i class="fas fa-phone"></i> {{ __('adminlte::validation.attributes.phone') }}</label>
            <div class="input-group">
              <select id="phone_cc" name="country_code" class="form-control @error('country_code') is-invalid @enderror" style="max-width: 140px;">
                @include('partials.country-codes')
              </select>
              <input type="tel" id="phone" name="phone"
                     class="form-control @error('phone') is-invalid @enderror"
                     value="{{ old('phone', $user->phone) }}"
                     placeholder="{{ __('adminlte::validation.attributes.phone') }}"
                     inputmode="tel" autocomplete="tel">
            </div>
            @error('country_code') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            @error('phone')        <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
          </div>

          {{-- Password opcional --}}
          <div class="form-group mb-3">
            <label class="form-label"><i class="fas fa-lock"></i> {{ __('adminlte::validation.attributes.password') }}</label>
            <div class="input-group">
              <input type="password" id="password" name="password"
                     class="form-control @error('password') is-invalid @enderror"
                     placeholder="{{ __('adminlte::validation.attributes.password') }}" autocomplete="new-password">
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

          <div class="form-group mb-1">
            <label class="form-label"><i class="fas fa-lock"></i> {{ __('adminlte::validation.attributes.password_confirmation') }}</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   class="form-control @error('password_confirmation') is-invalid @enderror"
                   placeholder="{{ __('adminlte::validation.attributes.password_confirmation') }}" autocomplete="new-password">
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
@stop

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
      if (input.type === 'password') { input.type = 'text'; icon.classList.replace('fa-eye','fa-eye-slash'); }
      else { input.type = 'password'; icon.classList.replace('fa-eye-slash','fa-eye'); }
    });
  });

  const cc = document.getElementById('phone_cc');

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
  cc.addEventListener('blur', collapseLabels);

  collapseLabels();
});
</script>

@if(session('success'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  Swal.fire({ icon: 'success', title: @json(session('success')), showConfirmButton: false, timer: 1800 });
</script>
@endif
@endpush
