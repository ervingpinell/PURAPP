@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@section('title', 'Registro')
@section('auth_header', 'Crear cuenta de cliente')

@section('auth_body')
    <form action="{{ route('register.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <input type="text" name="full_name" class="form-control" placeholder="Nombre completo" value="{{ old('full_name') }}" required autofocus>
        </div>

        <div class="mb-3">
            <input type="email" name="email" class="form-control" placeholder="Correo electrónico" value="{{ old('email') }}" required>
        </div>

        <div class="mb-3">
            <input type="text" name="phone" class="form-control" placeholder="Teléfono" value="{{ old('phone') }}" required>
        </div>

        <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
        </div>

        <div class="mb-3">
            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirmar contraseña" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
    </form>
@endsection

@section('auth_footer')
    <a href="{{ route('login') }}" class="text-center">¿Ya tienes una cuenta? Iniciar sesión</a>

        {{-- Back button --}}
    <div class="mt-3 text-center">
        <a href="{{ url('/') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> {{ __('adminlte::adminlte.back') }}
        </a>
    </div>
@endsection
