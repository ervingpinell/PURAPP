{{-- resources/views/profile/edit.blade.php --}}

@extends('adminlte::auth.auth-page')

@section('title', __('adminlte::adminlte.edit_profile'))

@section('auth_header')
    <h3 class="text-center mb-0">{{ __('adminlte::adminlte.edit_profile') }}</h3>
@stop

@section('auth_body')
    @if(session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="full_name">{{ __('adminlte::adminlte.full_name') }}</label>
            <input type="text" name="full_name" id="full_name"
                   class="form-control @error('full_name') is-invalid @enderror"
                   value="{{ old('full_name', auth()->user()->full_name) }}" required
                   placeholder="{{ __('adminlte::adminlte.full_name') }}">
            @error('full_name') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label for="email">{{ __('adminlte::adminlte.email') }}</label>
            <input type="email" name="email" id="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', auth()->user()->email) }}" required
                   placeholder="{{ __('adminlte::adminlte.email') }}">
            @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label for="phone">{{ __('adminlte::adminlte.phone') }}</label>
            <input type="text" name="phone" id="phone"
                   class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone', auth()->user()->phone) }}"
                   placeholder="{{ __('adminlte::adminlte.phone') }}">
            @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3 position-relative">
            <label for="password">
                {{ __('adminlte::adminlte.password') }}
                <small class="text-muted">({{ __('adminlte::adminlte.optional') ?? 'opcional' }})</small>
            </label>
            <input type="password" name="password" id="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="{{ __('adminlte::adminlte.password') }}">
            <a href="#" class="toggle-password position-absolute" 
               style="top: 40px; right: 15px; cursor: pointer;" data-target="password" tabindex="-1">
                <i class="fas fa-eye"></i>
            </a>
            @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        {{-- Password requirements --}}
        <ul class="mb-3 small text-muted" id="password-requirements">
            <li id="req-length" class="text-muted">{{ __('adminlte::adminlte.password_min_length') ?? 'Mínimo 8 caracteres' }}</li>
            <li id="req-special" class="text-muted">{{ __('adminlte::adminlte.password_special_char') ?? 'Al menos un carácter especial (!@#$%^&*)' }}</li>
            <li id="req-number" class="text-muted">{{ __('adminlte::adminlte.password_number') ?? 'Al menos un número' }}</li>
        </ul>

        <div class="mb-3 position-relative">
            <label for="password_confirmation">{{ __('adminlte::adminlte.retype_password') }}</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                   placeholder="{{ __('adminlte::adminlte.retype_password') }}">
            <a href="#" class="toggle-password position-absolute"
               style="top: 40px; right: 15px; cursor: pointer;" data-target="password_confirmation" tabindex="-1">
                <i class="fas fa-eye"></i>
            </a>
        </div>

        <button type="submit" class="btn btn-info w-100">
            <i class="fas fa-save"></i> {{ __('adminlte::adminlte.save') }}
        </button>
    </form>
@stop

@section('auth_footer')
    <div class="text-center">
        <a href="{{ route('home') }}" class="text-muted"><i class="fas fa-arrow-left"></i> {{ __('adminlte::adminlte.back') }}</a>
    </div>
@stop

@section('adminlte_js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const reqLength = document.getElementById('req-length');
    const reqSpecial = document.getElementById('req-special');
    const reqNumber = document.getElementById('req-number');

    if(passwordInput){
        passwordInput.addEventListener('input', function () {
            const value = passwordInput.value;
            reqLength.className = value.length >= 8 ? 'text-success' : 'text-muted';
            reqSpecial.className = /[!@#$%^&*(),.?":{}|<>]/.test(value) ? 'text-success' : 'text-muted';
            reqNumber.className = /\d/.test(value) ? 'text-success' : 'text-muted';
        });
    }

    document.querySelectorAll('.toggle-password').forEach(toggle => {
        toggle.addEventListener('click', function(e){
            e.preventDefault();
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            if(input){
                if(input.type === 'password'){
                    input.type = 'text';
                    this.querySelector('i').classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    input.type = 'password';
                    this.querySelector('i').classList.replace('fa-eye-slash', 'fa-eye');
                }
            }
        });
    });
});
</script>
@stop
